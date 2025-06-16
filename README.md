# 📬 XKCD Email Subscription System

A lightweight, fully automated email subscription platform built with **vanilla PHP** and **file-based storage** — no database required.  
It delivers a random **XKCD comic** every day via email to verified users. Includes a secure **email verification flow**, **unsubscribe mechanism**, and uses **CRON** for scheduled automation.

---

## 🚀 Features

- ✅ **Simple Email Registration with OTP Verification**  
  Users sign up using their email and confirm their identity through a secure 6-digit One-Time Password (OTP).

- 📬 **Daily XKCD Comic Delivery**  
  Sends a fresh, randomly selected XKCD comic to subscribers every 24 hours in a neatly formatted HTML email.

- 🔄 **CRON-Driven Automation**  
  Entire process — from fetching the comic to sending emails — is managed through CRON jobs with zero manual effort.

- 🔐 **Secure and Easy Unsubscribe Process**  
  Allows users to opt out at any time using a secure confirmation flow to prevent accidental or unauthorized unsubscribes.

- 📁 **Flat File Storage (No Database)**  
  All user data and logs are stored in plain files — ideal for small projects and easy to deploy on shared hosting.

- ✉️ **PHPMailer + Gmail SMTP Integration**  
  Ensures reliable and authenticated email delivery using the trusted PHPMailer library and Gmail SMTP.

- 📊 **Logging and Monitoring**  
  Maintains detailed logs of email activity and CRON executions for easy debugging and transparency.

---

## 🛠️ Tech Stack

- PHP (Vanilla)  
- PHPMailer  
- Gmail SMTP  
- CRON (Linux-based scheduling)  
- Flat file system (JSON or TXT)  
