# 🏥 Smart Health Insurance Claim Processing & Fraud Detection System

## 📌 Project Overview

Smart Health Insurance Claim Processing & Fraud Detection System is a web-based application developed to automate the health insurance claim process and identify potentially fraudulent claims. The system provides role-based access for Hospitals, Policyholders, Insurance Officers, and Administrators.

The project improves claim management efficiency, reduces manual workload, enhances transparency, and supports fraud detection through risk assessment mechanisms.

---

## 🎯 Objectives

- Automate insurance claim submission and processing.
- Reduce manual paperwork and processing delays.
- Detect suspicious or fraudulent claims.
- Provide real-time claim tracking.
- Improve communication between hospitals, policyholders, and insurance officers.
- Generate reports and analytics for decision-making.

---

## 👥 User Roles

### 🏥 Hospital
- Submit new insurance claims.
- Upload supporting documents.
- View claim history.
- Track claim status.

### 👤 Policyholder
- Track claim progress.
- View claim details.
- Download claim reports.
- View approval and payment status.

### 👨‍💼 Insurance Officer
- Review submitted claims.
- Verify policy information.
- Approve or reject claims.
- Request additional information.
- Assess fraud risk levels.

### 👨‍💻 Administrator
- Manage users.
- View system reports.
- Monitor claim activities.
- Access audit logs.

---

## ✨ Key Features

### Claim Management
- Online claim submission
- Claim review and approval workflow
- Claim history tracking
- Document upload support

### Fraud Detection
- Risk score calculation
- Fraud risk assessment dashboard
- Suspicious claim identification
- Risk-level categorization

### Reporting & Analytics
- Claim statistics
- Approval/Rejection analysis
- Fraud risk reports
- Downloadable claim reports

### Security Features
- User authentication
- Role-based access control
- Audit logs
- Session management

---

## 🛠 Technologies Used

### Frontend
- HTML5
- CSS3
- Bootstrap 5
- JavaScript

### Backend
- PHP

### Database
- MySQL

### Server
- XAMPP (Apache)

### Version Control
- Git
- GitHub

---

## 📂 Project Structure


```text
SmartHealthInsuranceSystem
│
├── admin/
│   └── manage_users.php
│
├── hospital/
│   └── submit_claim.php
│
├── officer/
│   └── review_claims.php
│
├── policyholder/
│   └── track_claim.php
│
├── includes/
│   ├── auth.php
│   ├── header.php
│   └── sidebar.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   │
│   └── js/
│       └── script.js
│
├── dashboard.php
├── database.php
├── download_report.php
├── audit_logs.php
├── login.php
├── logout.php
├── index.php
└── README.md
```

---

## 🗄 Database Tables

- Users
- Claims
- Policies
- Hospitals
- Notifications
- Audit Logs

---

## 🔄 System Workflow

1. Hospital submits a claim.
2. Supporting documents are uploaded.
3. Fraud score is calculated.
4. Insurance Officer reviews the claim.
5. Claim is approved or rejected.
6. Policyholder tracks claim status.
7. Reports are generated and downloaded.

---

## 📊 Modules

### Hospital Dashboard
- Claim submission
- Claim tracking
- Notifications

### Policyholder Dashboard
- Claim status tracking
- Claim history
- Report download

### Officer Dashboard
- Review claims
- Fraud detection
- Policy verification
- Claim approval/rejection

### Admin Dashboard
- User management
- Reports & analytics
- Audit monitoring

---

## 🔍 Fraud Detection Logic

The system evaluates claims based on:

- Claim amount
- Policy validity
- Coverage limits
- Hospital eligibility
- Multiple claims in a short period

Risk scores are generated and categorized as:

| Risk Score | Category |
|------------|----------|
| 0 - 30 | Low Risk |
| 31 - 70 | Medium Risk |
| 71 - 100 | High Risk |

---

## 🚀 Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/shumaila804/SmartHealthInsuranceSystem.git
```

### Step 2: Move Project Folder

Move the project folder to:

```text
xampp/htdocs/
```

### Step 3: Start XAMPP Services

Start the following services from the XAMPP Control Panel:

* Apache
* MySQL

### Step 4: Import Database

Open **phpMyAdmin** and import the project database file.

### Step 5: Run the Project

Open the following URL in your browser:

```text
(http://localhost/SMARTHEALTHINSURANCE/)
```

---

## 📈 Future Enhancements

* 🤖 Machine Learning Based Fraud Prediction
* 📧 Email Notification System
* 📱 SMS Alert Service
* 💳 Payment Gateway Integration
* 📲 Mobile Application Support
* 📊 Advanced Analytics Dashboard
