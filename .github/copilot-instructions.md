# P2P Communication Laravel Project

This is a Laravel-based P2P communication application designed for computer networking course projects with Wireshark analysis capabilities.

## Project Status: ✅ COMPLETED

- [x] ✅ **Copilot Instructions Created**
- [x] ✅ **Requirements Clarified** - Laravel P2P communication app with PIN authentication, messaging, file transfer, Tailwind CSS for network analysis project
- [x] ✅ **Project Scaffolded** - Laravel 12.x with React starter kit, converted to Blade templates
- [x] ✅ **Custom Features Implemented** - PIN authentication, messaging system, file transfer, P2P connectivity
- [x] ✅ **Extensions Installed** - Laravel Artisan extension for development
- [x] ✅ **Project Compiled** - Dependencies installed, migrations run, assets built successfully
- [x] ✅ **Development Task Created** - Laravel development server running on http://0.0.0.0:8000
- [x] ✅ **Documentation Complete** - Comprehensive README.md with setup instructions and network analysis guide

## Features Implemented

### 🔐 Authentication System

- PIN-based authentication (4-digit)
- Session management with middleware protection
- User session tracking with unique session IDs

### 💬 P2P Messaging

- Real-time messaging between peers
- AJAX polling for message updates
- TCP-based communication for reliability
- JSON payload structure for Wireshark analysis

### 📁 File Transfer

- Image file upload/download functionality
- Drag & drop interface
- File metadata tracking (size, hash, type)
- Storage management with Laravel filesystem

### 🔍 Network Analysis Features

- Built-in network protocol information
- Wireshark filter suggestions
- TCP/HTTP traffic optimization for packet analysis
- OSI layer breakdown documentation

### 🎨 UI/UX

- Tailwind CSS responsive design
- Clean, modern interface
- Real-time status updates
- Network information display

## Tech Stack

- **Backend**: Laravel 12.x, PHP 8.4
- **Frontend**: Blade Templates, Tailwind CSS, Vanilla JavaScript
- **Database**: SQLite with comprehensive schema
- **Protocol**: TCP (HTTP/HTTPS)
- **Real-time**: AJAX polling

## Development Server

The Laravel development server is running on `http://0.0.0.0:8000` and accessible from other computers in the local network for P2P testing.

## Usage for Network Analysis

This application is specifically designed for computer networking coursework:

1. **Setup**: Two students can run the app on different computers in the same LAN
2. **Connection**: Exchange session IDs to establish P2P communication
3. **Analysis**: Use Wireshark to capture and analyze TCP traffic, HTTP requests, JSON payloads, and file transfers
4. **Learning**: Study OSI layer encapsulation, protocol headers, and network communication patterns

Perfect for hands-on learning of network protocols and packet analysis! 🌐
