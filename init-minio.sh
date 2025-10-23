#!/bin/bash
set -e

echo "Waiting for MinIO to be ready..."
MAX_ATTEMPTS=30
ATTEMPT=0

# Wait and configure MinIO client
until mc alias set myminio http://minio:9000 "${MINIO_ROOT_USER}" "${MINIO_ROOT_PASSWORD}" > /dev/null 2>&1 || [ $ATTEMPT -eq $MAX_ATTEMPTS ]; do
    ATTEMPT=$((ATTEMPT + 1))
    echo "MinIO is not ready yet, waiting... (attempt $ATTEMPT/$MAX_ATTEMPTS)"
    sleep 3
done

if [ $ATTEMPT -eq $MAX_ATTEMPTS ]; then
    echo "ERROR: Could not connect to MinIO after $MAX_ATTEMPTS attempts!"
    exit 1
fi

echo "MinIO is ready! Configuring CORS..."

# Verify tools are available
echo "Checking required tools..."
command -v mc >/dev/null 2>&1 || { echo "ERROR: MinIO Client (mc) not found!"; exit 1; }
command -v aws >/dev/null 2>&1 || { echo "ERROR: AWS CLI not found!"; exit 1; }
echo "All required tools are available."

echo "Creating bucket if it doesn't exist..."
mc mb myminio/${AWS_BUCKET} --ignore-existing

echo "Setting up CORS policy for bucket: ${AWS_BUCKET}"

# Create CORS policy configuration in AWS S3 JSON format for aws-cli
cat > /tmp/cors.json <<'EOF'
{
  "CORSRules": [
    {
      "AllowedOrigins": ["*"],
      "AllowedMethods": ["GET", "HEAD", "PUT", "POST", "DELETE"],
      "AllowedHeaders": ["*"],
      "ExposeHeaders": ["ETag", "x-amz-request-id", "x-amz-id-2"],
      "MaxAgeSeconds": 3000
    }
  ]
}
EOF

# Apply CORS configuration using AWS CLI
echo "Applying CORS configuration using AWS S3 API..."
AWS_ACCESS_KEY_ID="${MINIO_ROOT_USER}" \
AWS_SECRET_ACCESS_KEY="${MINIO_ROOT_PASSWORD}" \
aws --endpoint-url http://minio:9000 \
    s3api put-bucket-cors \
    --bucket "${AWS_BUCKET}" \
    --cors-configuration file:///tmp/cors.json

echo "Setting public read policy for bucket..."
mc anonymous set download myminio/${AWS_BUCKET}

echo ""
echo "Verifying CORS configuration..."
AWS_ACCESS_KEY_ID="${MINIO_ROOT_USER}" \
AWS_SECRET_ACCESS_KEY="${MINIO_ROOT_PASSWORD}" \
aws --endpoint-url http://minio:9000 \
    s3api get-bucket-cors \
    --bucket "${AWS_BUCKET}" || echo "Warning: Could not retrieve CORS config for verification"

echo ""
echo "✅ MinIO CORS configuration completed successfully!"
echo "Bucket: ${AWS_BUCKET}"
echo "CORS: Enabled for all origins (*)"
echo "Public read: Enabled"
echo ""
echo "You can verify CORS is working by:"
echo "  curl -I -X OPTIONS https://s3-kafedra.phfk.college/${AWS_BUCKET}/"
