# Chatify - Real-Time Chat Application with CI/CD

Chatify is a real-time chat application built using React.js for the frontend and Node.js, Express.js with Socket.IO for the backend server, with a complete CI/CD pipeline for AWS deployment.

## 📁 Project Structure

```
├── Chatify/                    # 🚀 Main Application
│   ├── frontend/              # ⚛️  React.js frontend
│   ├── server/               # 🟢 Node.js backend with Socket.IO
│   ├── Dockerfile            # 🐳 Application containerization
│   ├── docker-compose.yml    # 🔧 Local services orchestration
│   └── Jenkinsfile          # 🔄 CI/CD pipeline definition
├── infrastructure/           # 🏗️  DevOps & Infrastructure
│   ├── terraform/           # ☁️  AWS infrastructure as code
│   ├── nagios/             # 📊 Application monitoring
│   ├── scripts/            # 🛠️  Deployment automation
│   └── github-actions/     # ⚡ CI/CD workflow templates
├── .github/workflows/      # 🤖 GitHub Actions (auto-generated)
└── deploy.sh              # 🚀 Main deployment script
```

## 🎯 Quick Start

### Option 1: One-Command Deployment
```bash
./deploy.sh
```

### Option 2: Step-by-Step
```bash
# 1. Setup prerequisites
infrastructure/scripts/setup-prerequisites.sh

# 2. Configure AWS
aws configure

# 3. Deploy everything
infrastructure/scripts/execute-deployment.sh
```

## Features

- **Real-time Messaging:** Instantly send and receive messages.
- **Multi-User Chat :** Multiple users can login and chat.
- **Responsive Design:** Works seamlessly across devices.

## Technologies Used

- **Frontend:**
  - React.js
  - CSS3

- **Backend:**
  - Node.js
  - Express.js
  - Socket.IO

## Installation

To run this project locally, follow these steps:

1. **Clone the repository:**

   ```bash
   git clone https://github.com/AtharvaKulkarniIT/Chatify.git
   ```

2. **Navigate to the frontend directory:**

   ```bash
   cd Chatify/frontend
   ```

3. **Install frontend dependencies:**

   ```bash
   npm install
   ```

4. **Navigate to the server directory:**

   ```bash
   cd ../server
   ```

5. **Install server dependencies:**

   ```bash
   npm install
   ```

## Running the Application

To start the frontend and backend servers:

### Frontend

1. **Open a new terminal and navigate to the frontend directory:**

   ```bash
   cd Chatify/frontend
   ```

2. **Start the frontend server:**

   ```bash
   npm start
   ```

3. **Open your browser and navigate to:**

   ```
   http://localhost:3000
   ```

   Open at least two tabs to simulate a chat room environment.

### Backend

1. **Open another terminal and navigate to the server directory:**

   ```bash
   cd Chatify/server
   ```

2. **Start the backend server:**

   ```bash
   npm start
   ```

## Usage

- Enter your unique username in each tab and start chatting.
- Messages are displayed in real-time with different styles for your messages and others'.


## UI
**Login**
![Login](https://drive.google.com/uc?export=download&id=1Xd7gjzFwpSlG8mLaDZwegbdEejXtB1s3)
<br/><br/>
**Chat page**
![Real-time chat](https://drive.google.com/uc?export=download&id=1EtxAVmU6Wn3b0EMuTosbbWfnFLSSZgVG)

## Contributing

Contributions are welcome! Fork the repository and submit a pull request for any features or fixes.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
