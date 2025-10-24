<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\Technology;
use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            ['slug' => 'php', 'name' => 'PHP', 'link' => 'https://www.php.net'],
            ['slug' => 'laravel', 'name' => 'Laravel', 'link' => 'https://laravel.com'],
            ['slug' => 'symfony', 'name' => 'Symfony', 'link' => 'https://symfony.com'],
            ['slug' => 'java', 'name' => 'Java', 'link' => 'https://www.java.com'],
            ['slug' => 'spring', 'name' => 'Spring Framework', 'link' => 'https://spring.io'],
            ['slug' => 'spring-boot', 'name' => 'Spring Boot', 'link' => 'https://spring.io/projects/spring-boot'],
            ['slug' => 'javafx', 'name' => 'JavaFX', 'link' => 'https://openjfx.io'],
            ['slug' => 'csharp', 'name' => 'C#', 'link' => 'https://learn.microsoft.com/dotnet/csharp'],
            ['slug' => 'dotnet', 'name' => '.NET', 'link' => 'https://dotnet.microsoft.com'],
            ['slug' => 'asp-net', 'name' => 'ASP.NET', 'link' => 'https://dotnet.microsoft.com/apps/aspnet'],
            ['slug' => 'javascript', 'name' => 'JavaScript', 'link' => 'https://developer.mozilla.org/docs/Web/JavaScript'],
            ['slug' => 'typescript', 'name' => 'TypeScript', 'link' => 'https://www.typescriptlang.org'],
            ['slug' => 'nodejs', 'name' => 'Node.js', 'link' => 'https://nodejs.org'],
            ['slug' => 'express', 'name' => 'Express.js', 'link' => 'https://expressjs.com'],
            ['slug' => 'nestjs', 'name' => 'NestJS', 'link' => 'https://nestjs.com'],
            ['slug' => 'react', 'name' => 'React', 'link' => 'https://react.dev'],
            ['slug' => 'nextjs', 'name' => 'Next.js', 'link' => 'https://nextjs.org'],
            ['slug' => 'vue', 'name' => 'Vue.js', 'link' => 'https://vuejs.org'],
            ['slug' => 'nuxt', 'name' => 'Nuxt', 'link' => 'https://nuxt.com'],
            ['slug' => 'angular', 'name' => 'Angular', 'link' => 'https://angular.io'],
            ['slug' => 'svelte', 'name' => 'Svelte', 'link' => 'https://svelte.dev'],
            ['slug' => 'python', 'name' => 'Python', 'link' => 'https://www.python.org'],
            ['slug' => 'django', 'name' => 'Django', 'link' => 'https://www.djangoproject.com'],
            ['slug' => 'flask', 'name' => 'Flask', 'link' => 'https://flask.palletsprojects.com'],
            ['slug' => 'fastapi', 'name' => 'FastAPI', 'link' => 'https://fastapi.tiangolo.com'],
            ['slug' => 'mysql', 'name' => 'MySQL', 'link' => 'https://www.mysql.com'],
            ['slug' => 'postgresql', 'name' => 'PostgreSQL', 'link' => 'https://www.postgresql.org'],
            ['slug' => 'mongodb', 'name' => 'MongoDB', 'link' => 'https://www.mongodb.com'],
            ['slug' => 'redis', 'name' => 'Redis', 'link' => 'https://redis.io'],
            ['slug' => 'sqlite', 'name' => 'SQLite', 'link' => 'https://www.sqlite.org'],
            ['slug' => 'mariadb', 'name' => 'MariaDB', 'link' => 'https://mariadb.org'],
            ['slug' => 'oracle', 'name' => 'Oracle Database', 'link' => 'https://www.oracle.com/database'],
            ['slug' => 'mssql', 'name' => 'Microsoft SQL Server', 'link' => 'https://www.microsoft.com/sql-server'],
            ['slug' => 'docker', 'name' => 'Docker', 'link' => 'https://www.docker.com'],
            ['slug' => 'kubernetes', 'name' => 'Kubernetes', 'link' => 'https://kubernetes.io'],
            ['slug' => 'aws', 'name' => 'Amazon Web Services', 'link' => 'https://aws.amazon.com'],
            ['slug' => 'azure', 'name' => 'Microsoft Azure', 'link' => 'https://azure.microsoft.com'],
            ['slug' => 'gcp', 'name' => 'Google Cloud Platform', 'link' => 'https://cloud.google.com'],
            ['slug' => 'vercel', 'name' => 'Vercel', 'link' => 'https://vercel.com'],
            ['slug' => 'netlify', 'name' => 'Netlify', 'link' => 'https://www.netlify.com'],
            ['slug' => 'heroku', 'name' => 'Heroku', 'link' => 'https://www.heroku.com'],
            ['slug' => 'git', 'name' => 'Git', 'link' => 'https://git-scm.com'],
            ['slug' => 'github', 'name' => 'GitHub', 'link' => 'https://github.com'],
            ['slug' => 'gitlab', 'name' => 'GitLab', 'link' => 'https://gitlab.com'],
            ['slug' => 'tailwind', 'name' => 'Tailwind CSS', 'link' => 'https://tailwindcss.com'],
            ['slug' => 'bootstrap', 'name' => 'Bootstrap', 'link' => 'https://getbootstrap.com'],
            ['slug' => 'sass', 'name' => 'Sass', 'link' => 'https://sass-lang.com'],
            ['slug' => 'webpack', 'name' => 'Webpack', 'link' => 'https://webpack.js.org'],
            ['slug' => 'vite', 'name' => 'Vite', 'link' => 'https://vitejs.dev'],
        ];

        foreach ($technologies as $technology) {
            Technology::query()->create([
                'slug' => $technology['slug'],
                'name' => $technology['name'],
                'description' => null,
                'image' => null,
                'link' => $technology['link'],
            ]);
        }
    }
}
