# Real Estate Management System (REMS) - RealHouse
## Project Overview
RealHouse is a comprehensive Real Estate Management System (REMS) designed to streamline various aspects of real estate operations, including property listings, user management, predictive analytics for property prices, and more. This system includes a machine learning model to predict house prices based on various factors such as location, title, number of bathrooms, bedrooms, and parking spaces.

## Features
- User Management: Allows for user registration and login, supporting different roles such as Tenants and Realtors.
- Property Management: Users can list, view, and search for properties based on various criteria.
- Price Prediction: Integrates a machine learning model to predict property prices based on input features.
- User Activity Tracking: Records user actions, including login and logout activities, for auditing and analysis.

## Technologies Used
- Frontend: HTML, CSS, JavaScript
- Backend: PHP (Vanilla)
- Database: MySQL
- Machine Learning: Python, scikit-learn, pandas, category_encoders
- API Communication: RESTful API using PHP for backend and Python for model prediction

# Project Structure

RealHouse/
├── backend/
│   ├── config.php
│   ├── signup.php
│   ├── login.php
│   ├── user_actions.php
│   ├── model_conn.php
│   └── ... (other PHP files)
├── frontend/
│   ├── index.html
│   ├── login.html
│   ├── signup.html
│   ├── assets
│   └── ... (JavaScript, CSS, and other  files)
├── model/
│   ├── Prediction.py
│   ├── model.pkl
│   └── Wazobia.ipynb
└── README.md


## Installation and Setup

### 1. Clone the Repository
```bash
git clone https://github.com/your-repository/RealHouse.git
cd RealHouse
``` 

