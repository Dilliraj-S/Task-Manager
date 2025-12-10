<h1 align="center">Task Manager</h1>

## Introduction
Task Manager is an open-source Laravel application designed to simplify the process of managing project alone with task. The task page is designed like clickup or trello board, so developer will get a very flexbility to handle all This documentation provides a step-by-step guide on how to set up the project.

### Prerequisites
- PHP 8.1 or higher
- Composer
- Laravel 10 or higher
- MySQL or any other supported database system

## Setup Instructions

### Step 1: Clone the Repository
```
git clone <your-repo-url>
cd <your-repo-folder>
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Configure Environment Variables
Duplicate the `.env.example` file and rename it to `.env`. Update the following variables:


### Step 4: Generate Application Key
```bash
php artisan key:generate
```

### Step 5: Run Migrations and Seed Database
```bash
php artisan migrate --seed
```

### Step 6: Serve the Application
```bash
php artisan serve
```

Access the application in your browser at `http://localhost:8000`.


## How to Use

### 1. Task Management
Task Manager allows users to efficiently manage projects and tasks through a user-friendly interface similar to ClickUp or Trello. Here are the main features:

1. **Login to the admin panel:**
    ```
    Email: task@tm.com
    Password: secret
    ```

2. **Projects:**
   - Create and manage multiple projects.
   - Assign tasks to specific projects to keep everything organized.

3. **Tasks:**
   - Add, edit, and delete tasks within a project.
   - Use drag-and-drop functionality to move tasks between different stages or statuses.

4. **Notes:**
   - Attach notes to tasks or projects for additional details and context.
   - Keep track of important information that doesn't fit into tasks.

5. **Reminders:**
   - Set reminders for tasks to ensure deadlines are met.

6. **Routines:**
   - Define routine tasks that need to be done regularly.

7. **Files:**
   - Upload and attach files to tasks or projects.
   - Easily access all necessary documents and resources in one place.

## Contributing
For issues or inquiries, please open an issue or pull request in this repository.
