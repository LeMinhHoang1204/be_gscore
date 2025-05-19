# G-Scores - Frontend Website

(Test Project)

## 📋 Description

This repository contains the backend source code for **G-Scores**, a platform to query and analyze student scores from the 2024 Vietnam National High School Exam.
The backend is built with **Laravel 10** and **MySQL 8.0**, exposing RESTful APIs for score lookup, statistics, and top student listings. The project supports both local development and production deployment via Docker.

## 🌐 Product: [https://fe-gscores.vercel.app](https://fe-gscores.vercel.app)

## ✨ Features


### ✅ Must Have

- **Data Migration**: Raw file [diem_thi_thpt_2024.csv](https://github.com/GoldenOwlAsia/webdev-intern-assignment-3/blob/main/dataset/diem_thi_thpt_2024.csv) is seeded and migrated to the database via backend (PHP Laravel).
- **Score Lookup API**: Users can check their exam results by entering their registration number. The API returns subject scores and total group scores.
- **Score Range Report API**: Statistics the number of students by subject and exam groups, and show subject-level score distribution within each group (e.g., click A00 to view scores for Math, Physics, Chemistry).
- **Top 10 by Group API**: Retrieves the top 10 students of multiple exam groups (A01, B00, D01...) based on total scores.
- **Validation & Error Handling**: Strict request validation is enforced (e.g., valid registration number, valid group name) and clear error messages for invalid/missing input.

### 🌟 Nice to Have

- Docker setup
- Deployed to **Digital Ocean**


## 🛠️ Tech Stack


| Layer          | Stack                            |
|----------------|----------------------------------|
| **Framework**  | PHP Laravel                     |
| **Database**   | MySQL        |
| **Authentication**     | JWT (JSON Web Token)                       |
| **Containerization**    | Docker                 |


##  Installation Guide


### Prerequisites
- PHP >= 8.1 
- Composer
- Docker (recommended)

### Steps

1. **Clone the repository**:

   ```bash
    git clone https://github.com/LeMinhHoang1204/be_gscores.git
    cd be_gscores
   ```

2. **Set up environment variables**: 
- Copy environment templates 
    ```bash
    cp ../../.env.example ../../.env
   ```
- Create a `.env.mysql` file in the root directory with the following content:
   ```bash
  MYSQL_ROOT_PASSWORD=
  MYSQL_DATABASE=
  MYSQL_USER=
  MYSQL_PASSWORD=
   ```

3. **Start Docker containers**:

   ```bash
   cd docker/DockerCompose
   docker-compose build 
   docker-compose up -d
   ```

4. **Generate Laravel app key**:

   ```bash
   docker exec -it dockercompose_laravel.backend_1 php artisan key:generate
   ```

5. **Generate JWT secret key**:

    ```bash
    docker exec -it dockercompose_laravel.backend_1 php artisan jwt:secret
   ```

5. **Run migrations and seed data**:

    ```bash
    docker exec -it dockercompose_laravel.backend_1 php artisan migrate --seed
   ```
5. **Access the application**:

    ```bash
    The backend will be running on http://localhost:8080.
   ```
## 💌 Contact Information

Owner: Le Minh Hoang  
Email: leminhhoang.working@gmail.com

