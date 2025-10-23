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

echo "Creating bucket if it doesn't exist..."
mc mb myminio/${AWS_BUCKET} --ignore-existing

echo "Setting up CORS policy for bucket: ${AWS_BUCKET}"

# Create CORS policy configuration
cat > /tmp/cors.json <<EOF
{
  "CORSRules": [
    {
      "AllowedOrigins": ["*"],
      "AllowedMethods": ["GET", "HEAD", "PUT", "POST", "DELETE"],
      "AllowedHeaders": ["*"],
      "ExposeHeaders": ["ETag", "x-amz-request-id", "x-amz-id-2"]
    }
  ]
}
EOF

# Apply CORS configuration to the bucket
mc anonymous set-json /tmp/cors.json myminio/${AWS_BUCKET} || true

# Alternative: Set CORS via mc cors command (newer versions)
mc cors set /tmp/cors.json myminio/${AWS_BUCKET}

echo "Setting public read policy for bucket..."
mc anonymous set download myminio/${AWS_BUCKET}

echo "MinIO CORS configuration completed successfully!"
