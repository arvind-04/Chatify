const express = require('express');
const http = require('http');
const cors = require('cors');
const socketio = require('socket.io');
const path = require('path');

const app = express();

// Serve static files from React build
app.use(express.static(path.join(__dirname, 'public')));

// Handle React routing
app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

const server = http.createServer(app);
const io = socketio(server,{
    cors :{
        origin:"*"
    }
})

io.on("connection",(socket)=>{
    console.log('New client connected');

    socket.on("chat",(data) =>{
        io.emit("chat",data)
    })

    socket.on("disconnect",()=>{
        console.log('Client disconnected');
    })
})

server.listen(3000,()=>{
    console.log('Server listening on port 3000');
})