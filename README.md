# Task Manager API

A simple REST API for managing tasks, users, and categories, built with PHP using MVC architecture and PostgreSQL.

## Project Structure

```
task-manager/
├── public/
│   └── index.php
├── src/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── CategoryController.php
│   │   ├── TaskController.php
│   │   └── UserController.php
│   ├── core/
│   │   ├── Auth.php
│   │   ├── Database.php
│   │   └── Router.php
│   ├── enums/
│   │   ├── Priority.php
│   │   ├── Role.php
│   │   └── Status.php
│   └── models/
│       ├── Category.php
│       ├── Task.php
│       └── User.php
├── .gitignore
├── composer.json
├── composer.lock
└── README.md
```

## Architecture

The project follows the MVC architecture.

- **Models** handle database operations using PDO.
- **Controllers** handle HTTP requests, authorization, input validation, and JSON responses.
- **Router** maps HTTP methods and URLs to controller actions.
- **Database** manages the PostgreSQL connection using PDO.
- **Auth** handles session authentication and admin authorization.
- **Enums** define roles, task priorities, and task statuses.

## Database

The PostgreSQL database contains four main tables:

- `users`
- `tasks`
- `categories`
- `user_task`

### Relationships

- Users and tasks have a many-to-many relationship through `user_task`.
- Tasks and categories have a one-to-many relationship.
- A task can optionally belong to a category.
- `username` and `email` are unique in the `users` table.
- The `(user_id, task_id)` pair is unique in `user_task`.

## Authentication & Authorization

Authentication is handled using PHP sessions.

There are two roles:

### Admin

Admin can:

- login, and logout
- Manage users
- Manage tasks
- Manage categories
- Assign tasks to multiple users
- View all tasks

### Member

Member can:

- Register, login, and logout
- View assigned tasks
- Create tasks
- Update and delete assigned tasks
- View, update, and delete their own account
- View categories

Members cannot manage other users, categories, or task assignments.

When a member creates a task, the task is automatically assigned to them.

## API Endpoints

The API is tested and documented using Postman.

### Authentication

| Method | Endpoint    | Description    |
|--------|-------------|----------------|
| POST   | `/register` | Register member|
| POST   | `/login`    | Login          |
| DELETE | `/logout`   | Logout         |

### Tasks

| Method | Endpoint              | Description                            |
|--------|-----------------------|----------------------------------------|
| POST   | `/tasks`              | Create a task                          |
| GET    | `/tasks`              | Get tasks available to logged-in user  |
| GET    | `/tasks/{id}`         | Get a task                             |
| PUT    | `/tasks/{id}`         | Update a task                          |
| DELETE | `/tasks/{id}`         | Delete a task                          |
| POST   | `/tasks/{id}/assign`  | Assign users to a task — Admin only    |

Admin can manage all tasks. Members can only access tasks assigned to them.

### Users

| Method | Endpoint       | Description                |
|--------|----------------|----------------------------|
| POST   | `/users`       | Create a user — Admin only |
| GET    | `/users`       | Get all users — Admin only |
| GET    | `/users/{id}`  | Get a user                 |
| PUT    | `/users/{id}`  | Update a user              |
| DELETE | `/users/{id}`  | Delete a user              |

Members can only access and manage their own account.

### Categories

| Method | Endpoint            | Description                    |
|--------|---------------------|--------------------------------|
| POST   | `/categories`       | Create a category — Admin only |
| GET    | `/categories`       | Get categories                 |
| GET    | `/categories/{id}`  | Get a category                 |
| PUT    | `/categories/{id}`  | Update a category — Admin only |
| DELETE | `/categories/{id}`  | Delete a category — Admin only |

## HTTP Status Codes

| Status | Meaning                           |
|--------|-----------------------------------|
| 200    | Request successful                |
| 201    | Resource created                  |
| 400    | Invalid or missing data           |
| 401    | Authentication required or login failed |
| 403    | Access denied                     |
| 404    | Resource or route not found       |
| 409    | Resource conflict(deleting a category that is being used)|

## How to Run

1. **Install dependencies**

   ```bash
   composer install
   ```

2. **Configure `.env`**

   Create a `.env` file in the project root:

   ```
   DB_HOST=localhost
   DB_PORT=5432
   DB_NAME=task_manager
   DB_USER=your_username
   DB_PASSWORD=your_password
   ```

3. **Create the database**

   Create the PostgreSQL database and required tables, relationships, constraints, and enums.

4. **Start the server**

   ```bash
   php -S localhost:8000 -t public
   ```

5. **Create the first admin**

   `/register` always creates a user with the `member` role — there is no endpoint to create an admin directly. To create the first admin:

   - Register a normal user through `/register`.
   - Manually update that user's `role` to `admin` in the `users` table.
