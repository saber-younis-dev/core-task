# Laravel POSTS API with Role-Based Permissions

A Laravel 12 API implementation for a blog platform with role-based permissions, authentication, and background job processing.

## Features

- User authentication via Laravel Sanctum
- Role-based authorization with fine-grained permissions
- Post management with approval workflow
- Category management
- Event-driven architecture with queued jobs
- API resources for consistent responses

## System Requirements

- PHP 8.2 or higher
- Composer
- MySQL or compatible database
- Redis (optional, for queue processing)

## Installation

1. Clone the repository:

```bash
git clone https://github.com/saber-younis-dev/core-task
cd core-task
```

2. Install dependencies:

```bash
composer install
```

3. Create environment file and generate application key:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in `.env`

5. Run migrations and seeders:

```bash
php artisan migrate --seed
```

6. Start the queue worker:

```bash
php artisan queue:work
```

## Authentication

The API uses Laravel Sanctum for authentication. Users can:
- Register new accounts
- Login to receive authentication tokens
- Access protected resources using token authentication

## Authorization

The system implements a robust role-based permission system:

- **Roles**: Admin, Editor, User
- **Permissions**: Fine-grained permissions like `create_posts`, `approve_posts`, etc.
- Users can have multiple roles
- Roles can have multiple permissions

Authorization is handled through route middleware and Gate checks.

## API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login and receive token
- `GET /api/user` - Get authenticated user profile

### Posts
- `GET /api/posts` - List all posts (filtered by user permissions)
- `POST /api/posts` - Create a new post (requires `create_posts` permission)
- `GET /api/posts/{post}` - View a specific post
- `PUT /api/posts/{post}` - Update a post (requires ownership or `edit_others_posts` permission)
- `DELETE /api/posts/{post}` - Delete a post (requires ownership or `delete_posts` permission)
- `GET /api/posts/pending` - List pending posts (requires `approve_posts` permission)
- `POST /api/posts/{post}/review` - Review a post (approve/reject) (requires `approve_posts` permission)

### Categories
- `GET /api/categories` - List all categories
- `POST /api/categories` - Create category (admin only)

## Model Relationships

- User has many Posts (One-to-Many)
- User has many Roles, Roles have many Users (Many-to-Many)
- Role has many Permissions, Permissions have many Roles (Many-to-Many)
- Post has many Categories, Categories have many Posts (Many-to-Many)
- Post has many Comments, each Comment belongs to one User (One-to-Many)

## Events and Listeners

- `PostSubmittedForApproval` - Triggered when a user submits a post
    - Notifies all admin users to review the post
- `PostApproved` - Triggered when a post is approved
    - Notifies the author via email

## Background Jobs

- Post approval notifications are processed asynchronously
- Email notifications are sent via queued jobs

## Security Implementation

The API implements several layers of security:

1. **Authentication** via Laravel Sanctum
2. **Authorization** using middleware and Gate checks
3. **Validation** for all incoming requests
4. **Rate limiting** to prevent abuse

## Sample Users

The seeder creates these default users:

- Admin: admin@example.com (password: password)
- Editor: editor@example.com (password: password)
- Regular User: test@example.com (password: password)

## Configuration

The following environment variables can be configured:

```
QUEUE_CONNECTION=database  # Or redis for production
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Laravel 12 Specific Changes

- Middleware is registered in `bootstrap/app.php` instead of `app/Http/Kernel.php` (which no longer exists)
- Route middleware is applied directly in route definitions rather than controller constructors
- Authorization is implemented using Gate checks in controllers

## Development Notes

This implementation satisfies all the required tasks:

1. ✅ **API with Authentication**
    - Laravel Sanctum implementation
    - API resource controllers with validation
    - Role-based permissions system

2. ✅ **Event and Listener with Queue**
    - Registration events for new users
    - Post approval workflow with notifications

3. ✅ **Complex Model Relationships & Query Scopes**
    - All required relationship types implemented
    - Query scopes for filtering posts and users

4. ✅ **Middleware for Role-Based Access**
    - Custom permission middleware
    - Database-stored roles and permissions
    - Gate-based authorization

5. ✅ **Background Job for Post Approval**
    - Posts require approval before publishing
    - Queue jobs notify admins about pending posts
    - Events notify authors when posts are approved

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
