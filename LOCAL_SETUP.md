## 🚀 BarPro Premium — Local Development Environment Ready

Your Docker environment is now running! Here's what's set up:

---

## 📍 Access Points

| Service | URL | Credentials |
|---------|-----|-------------|
| **WordPress** | http://localhost:8080 | Install now →  |
| **PhpMyAdmin** | http://localhost:8081 | user: `wordpress`, pass: `wordpress` |
| **MySQL** | localhost:3306 | user: `wordpress`, pass: `wordpress` |

---

## ✅ What's Running

- **WordPress** 6.8 with PHP 8.1 Apache
- **MySQL** 8.0 database
- **PhpMyAdmin** for database management
- Theme mounted at: `/wp-content/themes/barpro-premium/`

---

## 🔧 Next Steps (Complete the Checklist)

### 1️⃣ WordPress Installation (2 min)

1. Open **http://localhost:8080** in your browser
2. You should see the WordPress installation screen
3. Follow the on-screen setup wizard:
   - Language: English
   - Database Name: `wordpress_db`
   - Username: `wordpress`
   - Password: `wordpress`
   - Database Host: `mysql`
4. Create admin account
5. Click **Install WordPress**

### 2️⃣ Activate BarPro Premium Theme

1. Log in to **http://localhost:8080/wp-admin**
2. Go to **Appearance → Themes**
3. Find **BarPro Premium** and click **Activate**
4. Theme will auto-create pages:
   - `/calculator` — Calculator
   - `/cocktails` — Cocktails
   - `/catering` — Catering Services
   - `/bar-catering` — Bar + Catering
   - `/team` — Team

### 3️⃣ Install ACF Pro

1. Go to **Plugins → Add New**
2. Upload `advanced-custom-fields-pro.zip`
3. Activate
4. Enter license key in **Custom Fields → Settings**

### 4️⃣ Configure SMTP for Email

Install **WP Mail SMTP** plugin:

1. **Plugins → Add New** → Search "WP Mail SMTP"
2. Install and Activate
3. **Settings → WP Mail SMTP**
4. Choose mailer:
   - **Gmail:** Easier to set up
   - **SendGrid:** More reliable for production
   - **Mailhog:** Docker testing (no setup)

### 5️⃣ Test the Calculator Page

1. Go to **http://localhost:8080/calculator**
2. Complete the calculator form
3. Verify calculation works
4. Submit lead form
5. Check **Dashboard → Leads** in admin

---

## 📋 Complete Deployment Checklist

**See:** `DEPLOYMENT_CHECKLIST.md` (in project root) for full step-by-step instructions including:

- Phase 1: Theme & Plugin Setup (ACF Pro, SMTP)
- Phase 2: Content Setup (Cocktails, Cases, Team)
- Phase 3: Feature Testing (Calculator, Leads, Responsive)
- Phase 4: Frontend Build (Vite)
- Phase 5: Quality Checks (SEO, Performance, Console Errors)
- Phase 6: Final Verification

---

## 🛠️ Docker Commands

```bash
# View containers
docker ps

# View logs
docker compose logs wordpress   # WordPress logs
docker compose logs mysql       # Database logs

# Access WordPress container shell
docker compose exec wordpress bash

# Stop containers
docker compose down

# Start containers
docker compose up -d

# Rebuild
docker compose up -d --build
```

---

## 📦 Build Vite Assets (for production)

When ready to go live:

```bash
npm install
npm run build

# Check if dist/ was created with manifest.json
dir assets\dist\
```

---

## 📝 Important Files Created

- `docker-compose.yml` — Container orchestration
- `.env.example` — Environment variables template
- `php.ini` — PHP configuration
- `.dockerignore` — Optimize Docker builds
- `DEPLOYMENT_CHECKLIST.md` — Full deployment guide

---

## ⚠️ Common Issues

### WordPress shows install page every refresh
- Wait 2-3 minutes for full initialization
- Check MySQL is healthy: `docker ps` (look for MySQL status)

### Can't connect to database
- Verify MySQL is running: `docker compose ps`
- Check credentials in `.env` file
- Restart: `docker compose restart`

### Port 8080 already in use
- Change WordPress port in `docker-compose.yml`:
  ```yaml
  ports:
    - "8082:80"  # Change 8080 to 8082
  ```
- Then: `docker compose up -d`

### MySQL permission issues
- Reset: `docker compose down -v`
- Start fresh: `docker compose up -d`

---

## 🎯 Success Criteria

Once you complete the checklist:

- ✅ WordPress installed
- ✅ BarPro theme activated
- ✅ ACF Pro installed
- ✅ SMTP configured
- ✅ Test content created
- ✅ Calculator works
- ✅ Lead submission works
- ✅ Email delivery works
- ✅ Vite build passes
- ✅ Mobile responsive verified

---

**Status:** Containers running ✓  
**Next:** Open http://localhost:8080 and complete installation
