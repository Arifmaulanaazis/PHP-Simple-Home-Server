---

# Simple Home Server Web Application

This project is a simple home server web application built with PHP that allows users to upload, delete, rename files and folders, and stream video and music. It is designed to run on a local server environment using either XAMPP or Nginx.

## Features

- **File Upload:** Easily upload files to your server.
- **File and Folder Deletion:** Remove files and directories with a simple interface.
- **Rename Files and Folders:** Change the names of your files and folders quickly.
- **Video and Music Streaming:** Stream your media files directly from the server.

## Getting Started

To set up this simple home server, follow the steps below for either XAMPP or Nginx.

### Prerequisites

- PHP (version 7.4 or higher recommended)
- XAMPP or Nginx installed on your machine

### Using XAMPP

1. **Install XAMPP:**
   - Download and install XAMPP from the [official website](https://www.apachefriends.org/index.html).

2. **Clone the Repository:**
   - Clone this repository into your XAMPP `htdocs` directory:
     ```bash
     cd /path/to/xampp/htdocs
     git clone https://github.com/Arifmaulanaazis/PHP-Simple-Home-Server.git
     ```

3. **Update Configuration:**
   - Open `index.php` and update the path to the directory you want to use:
     ```php
     $filePath = 'path/to/your/folder'; // Change this to your desired directory
     ```

4. **Start XAMPP:**
   - Launch the XAMPP control panel and start the Apache server.

5. **Access the Application:**
   - Open your web browser and navigate to `http://localhost/simple-home-server`.

### Using Nginx

1. **Install Nginx and PHP:**
   - Ensure that Nginx and PHP are installed on your system. You can find installation instructions for [Nginx](https://www.nginx.com/resources/wiki/start/) and [PHP](https://www.php.net/manual/en/install.php) on their respective websites.

2. **Clone the Repository:**
   - Clone this repository into your Nginx `html` directory:
     ```bash
     cd /usr/share/nginx/html
     git clone https://github.com/Arifmaulanaazis/simple-home-server.git
     ```

3. **Update Configuration:**
   - Open `index.php` and set the desired directory:
     ```php
     $filePath = 'path/to/your/folder'; // Change this to your desired directory
     ```

4. **Configure Nginx:**
   - Edit your Nginx configuration file (e.g., `/etc/nginx/sites-available/default`) to serve the application:
     ```nginx
     server {
         listen 80;
         server_name localhost;

         root /usr/share/nginx/html/simple-home-server;
         index index.php index.html index.htm;

         location / {
             try_files $uri $uri/ /index.php?$query_string;
         }

         location ~ \.php$ {
             include snippets/fastcgi-php.conf;
             fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
         }

         location ~ /\.ht {
             deny all;
         }
     }
     ```

5. **Restart Nginx:**
   - Restart the Nginx service to apply the changes:
     ```bash
     sudo systemctl restart nginx
     ```

6. **Access the Application:**
   - Open your web browser and navigate to `http://localhost`.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

## Contribution

Contributions are welcome! If you have ideas or improvements, feel free to submit a pull request or open an issue.

### How to Contribute

1. Fork the repository.
2. Create a new branch: `git checkout -b my-new-feature`.
3. Commit your changes: `git commit -am 'Add some feature'`.
4. Push to the branch: `git push origin my-new-feature`.
5. Submit a pull request.

## Contact

For any questions or inquiries, please contact me at [titandigitalsoft@gmail.com](mailto:titandigitalsoft@gmail.com).

---
