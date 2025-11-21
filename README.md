# P2P Communication Web Application

Aplikasi web Laravel untuk komunikasi P2P (Peer-to-Peer) yang dirancang khusus untuk project jaringan komputer dengan analisis Wireshark.

## 🚀 Features

- **PIN Authentication**: Sistem autentikasi dengan PIN 4 digit
- **Real-time Messaging**: Chat antar peer dalam jaringan lokal
- **File Transfer**: Upload dan download image files dengan support Drag & Drop
- **Message Control**: Fitur hapus pesan (Delete for me / Delete for everyone)
- **Network Analysis**: Built-in info untuk analisis protokol jaringan
- **TCP Protocol**: Menggunakan TCP untuk komunikasi yang reliable
- **Wireshark Ready**: Optimal untuk packet analysis

## 🛠️ Tech Stack

- **Backend**: Laravel 12.x dengan PHP 8.4
- **Frontend**: Blade Templates + Tailwind CSS + Vanilla JavaScript
- **Database**: SQLite (default)
- **Protocol**: TCP (HTTP/HTTPS)
- **Real-time**: AJAX Polling

## 📋 Prerequisites

- PHP >= 8.2
- Composer
- Node.js & npm
- Git

## 🔧 Installation & Setup

### 1. Clone & Install Dependencies

```bash
composer install
npm install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup

```bash
# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate
```

### 4. Build Assets

```bash
npm run build
# or for development
npm run dev
```

### 5. Start Development Server

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Server akan berjalan di: `http://localhost:8000`

## 🖥️ Usage

### Setup PIN (First Time)

1. Akses `http://localhost:8000`
2. Masukkan username (opsional) dan PIN 4 digit
3. Catat Session ID yang digenerate

### Connect to Peer

1. Pastikan kedua komputer dalam jaringan lokal yang sama
2. Akses aplikasi di komputer kedua: `http://[IP_KOMPUTER_1]:8000`
3. Setup PIN di komputer kedua
4. Di dashboard, masukkan Session ID peer untuk connect
5. Mulai chat dan transfer file
6. **Delete Message**: Hover pada pesan untuk melihat opsi hapus (Trash icon untuk 'Delete for me', Cross icon untuk 'Delete for everyone')

### Network Analysis dengan Wireshark

#### 1. Setup Wireshark

```bash
# Install Wireshark (macOS)
brew install --cask wireshark

# Install Wireshark (Linux)
sudo apt-get install wireshark
```

#### 2. Capture Network Traffic

1. Buka Wireshark
2. Pilih network interface yang aktif
3. Start capture
4. Filter traffic dengan: `tcp.port == 8000` atau `http`

#### 3. Analysis Points

- **TCP Handshake**: 3-way handshake saat establish connection
- **HTTP Requests**: POST/GET requests untuk messaging dan file transfer
- **JSON Payloads**: Data format yang dikirim
- **File Upload**: Multipart form data untuk file transfer
- **Polling**: Periodic GET requests untuk fetch messages

## 🔍 Network Protocol Details

### HTTP Endpoints

- `POST /setup-pin` - PIN setup
- `POST /login` - PIN authentication
- `POST /messages` - Send message
- `GET /messages/fetch` - Fetch messages (polling)
- `POST /files/upload` - Upload file
- `GET /files` - List recent files
- `GET /files/{id}/download` - Download file (Public access with ID token)
- `DELETE /messages/{id}/delete-for-me` - Delete message for self
- `DELETE /messages/{id}/delete-for-everyone` - Delete message for all peers

### Data Flow

1. **Authentication**: HTTP POST dengan PIN data
2. **Messaging**: HTTP POST/GET dengan JSON payloads
3. **File Transfer**: HTTP POST dengan multipart/form-data

### OSI Layer Analysis

- **Layer 7 (Application)**: HTTP/HTTPS protocols
- **Layer 6 (Presentation)**: JSON formatting, TLS encryption
- **Layer 5 (Session)**: HTTP sessions, PHP sessions
- **Layer 4 (Transport)**: TCP protocol, port 8000
- **Layer 3 (Network)**: IP routing dalam LAN
- **Layer 2 (Data Link)**: Ethernet frames
- **Layer 1 (Physical)**: Network interface

## 🧪 Testing Scenarios

### 1. Local Testing (Same Computer)

```bash
# Terminal 1
php artisan serve --host=127.0.0.1 --port=8000

# Terminal 2
php artisan serve --host=127.0.0.1 --port=8001
```

Akses: `localhost:8000` dan `localhost:8001`

### 2. LAN Testing (Different Computers)

```bash
# Komputer 1
php artisan serve --host=0.0.0.0 --port=8000

# Komputer 2 akses via IP
http://[IP_KOMPUTER_1]:8000
```

### 3. Wireshark Filters

```
# Basic HTTP traffic
http

# Specific port
tcp.port == 8000

# POST requests only
http.request.method == "POST"

# JSON content
http contains "application/json"

# File uploads
http.content_type contains "multipart/form-data"
```

## 📊 Database Schema

### user_pins

- `session_id`: UUID untuk identifikasi peer
- `pin_hash`: Hash dari PIN 4 digit
- `username`: Nama user (opsional)
- `ip_address`: IP address peer
- `is_active`: Status active peer

### messages

- `sender_session_id`: Session ID pengirim
- `receiver_session_id`: Session ID penerima
- `message`: Isi pesan
- `sender_ip` & `receiver_ip`: IP addresses
- `is_delivered`: Status delivered

### file_transfers

- `sender_session_id` & `receiver_session_id`: Session IDs
- `filename`: Generated filename
- `original_name`: Original filename
- `file_path`: Storage path
- `file_size`: Size in bytes
- `file_hash`: SHA256 hash
- `transfer_status`: pending/completed/failed

## 🔒 Security Features

- **PIN Hashing**: BCrypt untuk hash PIN
- **Session Management**: PHP sessions untuk auth
- **CSRF Protection**: Laravel CSRF tokens
- **File Validation**: Image only, max 10MB
- **SQL Injection Protection**: Eloquent ORM
- **XSS Protection**: Blade template escaping
- **Access Control**: Public file download menggunakan ID sebagai token untuk akses lintas device tanpa login ulang

## 🚨 Troubleshooting

### Port Already in Use

```bash
# Check port usage
lsof -i :8000

# Kill process
kill -9 [PID]
```

### Database Issues

```bash
# Reset database
php artisan migrate:fresh
```

### Permission Issues

```bash
# Fix storage permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 📝 Development Notes

### Project Structure

```
Laravel/
├── app/
│   ├── Http/Controllers/
│   │   ├── P2PController.php
│   │   ├── MessageController.php
│   │   └── FileTransferController.php
│   ├── Models/
│   │   ├── UserPin.php
│   │   ├── Message.php
│   │   └── FileTransfer.php
│   └── Http/Middleware/
│       └── P2PAuthMiddleware.php
├── resources/views/p2p/
│   ├── pin-setup.blade.php
│   ├── pin-login.blade.php
│   └── dashboard.blade.php
└── routes/web.php
```

### API Endpoints

- Authentication routes dengan middleware protection
- JSON responses untuk AJAX calls
- RESTful design patterns
- Error handling dengan proper HTTP status codes

## 🎯 Learning Objectives

Aplikasi ini dirancang untuk memahami:

1. **TCP/IP Stack**: Implementasi praktis protokol jaringan
2. **HTTP Protocol**: Request/response cycle, headers, methods
3. **Network Security**: Authentication, encryption, data validation
4. **Packet Analysis**: Menggunakan Wireshark untuk network debugging
5. **Client-Server Architecture**: Communication patterns
6. **Data Encapsulation**: OSI layer encapsulation dalam practice

## 📞 Support

Untuk pertanyaan atau issues terkait project jaringan komputer, silakan diskusikan dengan tim atau instructor.

---

**Happy Networking! 🌐**
