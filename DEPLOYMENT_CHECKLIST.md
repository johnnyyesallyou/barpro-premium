## BarPro Premium — Deployment Checklist (Local)

### Prerequisites
- Docker & Docker Compose installed
- Node.js 18+ (for Vite build)
- At least 4GB free disk space

---

## 🚀 Quick Start (5 minutes)

### 1. Start Docker Containers

```bash
cd C:\Users\Johnn\barpro-final

# Copy environment file
cp .env.example .env

# Start containers
docker compose up -d

# Wait for containers to be healthy (30-60 seconds)
docker compose ps
```

### 2. Initialize WordPress

Open **http://localhost:8080** in your browser:
1. Select language: **English** (or Russian if available)
2. Click **Let's go!**
3. Fill in database credentials (use values from `.env`):
   - Database Name: `wordpress_db`
   - Username: `wordpress`
   - Password: `wordpress`
   - Database Host: `mysql` (Docker container name)
   - Table Prefix: `wp_`
4. Click **Submit**
5. Create admin account:
   - Username: `admin`
   - Password: Use a strong password or `TestPass123!`
   - Email: `admin@localhost`
6. Click **Install WordPress**

### 3. Verify Installation

- WordPress admin: **http://localhost:8080/wp-admin**
- Database admin: **http://localhost:8081** (PhpMyAdmin)
- MySQL credentials: user=`wordpress`, password=`wordpress`

---

## ✅ Deployment Checklist

### Phase 1: Theme & Plugin Setup

#### ☐ 1.1 Activate Theme

1. Go to **Appearance → Themes**
2. Find "BarPro Premium" (should be in uploads or theme folder)
3. Click **Activate**
4. WordPress automatically creates required pages:
   - `/calculator` — Calculator page
   - `/cocktails` — Cocktails page
   - `/catering` — Catering page
   - `/bar-catering` — Bar Catering page
   - `/team` — Team page

**Verify:** Homepage loads without errors → ✓

#### ☐ 1.2 Install & Activate ACF Pro

**Without ACF Pro installed, the following will be empty:**
- Case Study custom fields (problem/solution/gallery)
- Team member fields (role, experience)
- Calculator hero images

**Install steps:**
1. Go to **Plugins → Add New**
2. Upload `advanced-custom-fields-pro.zip`
3. Click **Activate**
4. Enter your license key in **Custom Fields → Settings**

**Verify:** Custom Fields menu appears in WordPress admin → ✓

#### ☐ 1.3 Sync ACF JSON

If ACF field groups already exist in `acf-json/` folder:

1. Go to **Custom Fields → Sync**
2. If a sync tab appears (indicating changes), click **Sync**
3. Select all field groups and confirm

**Verify:** Field groups load in admin without conflicts → ✓

#### ☐ 1.4 Configure SMTP for Email Testing

Install **WP Mail SMTP** plugin for reliable email delivery:

1. Go to **Plugins → Add New**
2. Search "WP Mail SMTP by WPForms"
3. Install and Activate
4. Go to **Settings → WP Mail SMTP**

**Option A: Gmail (Free)**
- From Email: `your-email@gmail.com`
- From Name: `BarPro Premium`
- Mailer: **Gmail**
- Follow OAuth2 setup

**Option B: SendGrid (Free tier available)**
- Mailer: **SendGrid**
- API Key: Get from sendgrid.com
- From Email: `noreply@yourdomain.com`

**Option C: Mailhog (Docker - for testing only)**
- Mailer: **Other SMTP**
- Host: `mailhog`
- Port: `1025`
- No auth required

**Verify:** Send test email from settings → Check inbox ✓

---

### Phase 2: Content Setup

#### ☐ 2.1 Create Test Content

**Option 1: Quick Setup (5 min)**
Create minimal content to test functionality:
- 2-3 cocktails
- 1 case study
- 1 team member

**Option 2: Full Setup (30 min)**
Create realistic content matching your business:
- 15-20 cocktails with images
- 5-10 case studies
- 3-5 team members
- 2-3 testimonials

**Create Cocktails:**
1. Go to **Cocktails → Add New**
2. Title: "Mango Mojito"
3. Description: "Fresh mango, white rum, lime, mint"
4. Click **Publish**
5. Repeat for 2-3 more cocktails

**Create Cases:**
1. Go to **Cases → Add New**
2. Title: "Corporate Event 2024"
3. Description: "150 guests, luxury catering, 5-hour service"
4. Add featured image (1200x600px recommended)
5. Click **Publish**

**Create Team:**
1. Go to **Team → Add New**
2. Title: "John Doe"
3. Content: "Senior Bartender, 10+ years experience"
4. Add photo (400x400px recommended)
5. Click **Publish**

**Verify:**
- `/cocktails/` → Shows list of cocktails ✓
- `/cases/` → Shows case studies ✓
- `/team/` → Shows team members ✓

#### ☐ 2.2 Configure Customizer Settings

Go to **Appearance → Customize**:

**BarPro: Contacts**
- Phone: `+7 (999) 123-4567`
- Email: `hello@barpro.local`
- City: `Moscow`
- Address: `123 Main Street`
- WhatsApp: `79991234567`
- Telegram: `@barbarpro`
- Instagram: `@barpro_bar`
- VK: `vk.com/barpro`

**BarPro: Branding**
- Primary Gold: `#d4af37` (default)
- Dark Background: `#0a0a0a` (default)

**BarPro: Pricing** (for calculator)
- Base Price per Guest: `500`
- Price per Hour: `1000`
- Min Guests: `15`
- Max Guests: `500`

**Publish** all changes

**Verify:** Customizer settings save without errors → ✓

---

### Phase 3: Feature Testing

#### ☐ 3.1 Test Calculator Page

URL: **http://localhost:8080/calculator**

**Steps:**
1. Select event type (e.g., "Wedding")
2. Set guest count (e.g., "100")
3. Select services (e.g., "Bartender Service")
4. Review pricing calculation
5. Scroll to lead form
6. Enter test data:
   - Name: "Test User"
   - Phone: `+79991234567`
   - Email: `test@example.com`
7. Click **Submit**

**Success indicators:**
- No JavaScript errors in Console (F12) ✓
- Price calculation updates correctly ✓
- Form validates before submission ✓
- Success message appears ✓
- Lead appears in **Dashboard → Leads** ✓
- Email received (if SMTP configured) ✓

#### ☐ 3.2 Test Lead Submission & Email

1. Go to **Calculator** page
2. Submit test lead with all fields filled
3. Check **Dashboard → Leads** admin panel
4. Verify email received in configured inbox

**Success indicators:**
- Lead saved in WordPress ✓
- Email notification sent ✓
- Email contains correct data ✓
- No PHP errors in debug log ✓

#### ☐ 3.3 Test Case Study Page

URL: **http://localhost:8080/cases/**

**Steps:**
1. Open archive page
2. Click on any case study
3. Verify single case page loads
4. Check images display correctly
5. Verify back button/navigation works

**Success indicators:**
- Archive page lists all cases ✓
- Single case displays full content ✓
- Gallery/images load correctly ✓
- No broken links ✓

#### ☐ 3.4 Mobile Responsive Test

**Test at 3 breakpoints:**

**320px (Mobile):**
1. Open **http://localhost:8080** in DevTools (F12)
2. Set viewport to 320x568
3. Check hamburger menu opens
4. Calculator form is usable
5. No horizontal scroll

**375px (iPhone 11):**
1. Set viewport to 375x667
2. Check buttons are easily tappable (48px+)
3. Forms stack correctly

**768px (Tablet):**
1. Set viewport to 768x1024
2. Check layout adjusts properly
3. Grid items reflow correctly

**Success indicators:**
- Menu burger works ✓
- No horizontal scrolling ✓
- All buttons tappable ✓
- Calculator responsive ✓

---

### Phase 4: Frontend Build (Vite)

#### ☐ 4.1 Build Production Assets

This compiles JavaScript and CSS into optimized bundles.

```bash
cd C:\Users\Johnn\barpro-final

# Install dependencies (first time only)
npm install

# Build for production
npm run build
```

**Verify build succeeded:**
```bash
# Check if dist folder created
dir assets\dist\

# Should contain:
# - assets/dist/js/main.*.min.js
# - assets/dist/css/main.*.min.css
# - assets/dist/.vite/manifest.json
```

**Success indicators:**
- Build completes without errors ✓
- manifest.json file exists ✓
- No warnings in output ✓

#### ☐ 4.2 Verify Production Mode

After build completes, refresh the browser:

**http://localhost:8080** → View page source (Ctrl+U)

Check `<script>` tag:
- **Production:** `<script src="/wp-content/themes/barpro-premium/assets/dist/js/main.*.min.js"></script>`
- **Dev mode:** `<script src="/wp-content/themes/barpro-premium/assets/js/main.js"></script>`

**Success indicators:**
- Script path contains `/dist/` ✓
- Filename contains hash (e.g., `.a1b2c3d4.min.js`) ✓
- Pages load with minified bundles ✓

---

### Phase 5: Quality & Security Checks

#### ☐ 5.1 Check for PHP Errors

1. Go to **wp-content/debug.log** (in volume)
2. Or in admin: Check server logs for errors

```bash
docker compose logs wordpress | grep -i error
```

**Success:** No PHP errors or warnings → ✓

#### ☐ 5.2 Check for JavaScript Console Errors

1. Open any page
2. Press **F12** to open DevTools
3. Go to **Console** tab
4. Submit calculator form
5. Check for red error messages

**Success:** No JavaScript errors in console → ✓

#### ☐ 5.3 Test SEO Metadata

1. Open **Calculator** page
2. View page source (Ctrl+U)
3. Check for:
   - `<title>` tag with page title
   - `<meta name="description">` tag
   - `<meta property="og:title">` (OpenGraph)
   - `<meta property="og:image">` (OpenGraph)

**Success indicators:**
- All meta tags present ✓
- OpenGraph tags filled ✓
- No duplicate meta tags ✓

#### ☐ 5.4 Lighthouse Performance Audit

1. Open **http://localhost:8080** in Chrome
2. Press **F12**
3. Go to **Lighthouse** tab
4. Select **Performance** + **SEO**
5. Click **Analyze page load**

**Targets:**
- Performance: 80+
- SEO: 90+
- Accessibility: 80+

**Note:** Local Docker setup may have slower performance than production. Production servers should meet or exceed these targets.

---

### Phase 6: Deployment Checklist

#### ☐ 6.1 Final Verification

Before going live, verify all critical features:

**Calculator:**
- [ ] Page loads
- [ ] All steps work
- [ ] Calculations are correct
- [ ] Form validates
- [ ] Submission succeeds
- [ ] Lead saved
- [ ] Email sent
- [ ] Mobile responsive

**Cases:**
- [ ] Archive page loads
- [ ] Individual case loads
- [ ] Images display
- [ ] Pagination works (if >10 cases)

**Cocktails:**
- [ ] Archive page loads
- [ ] Filtering works
- [ ] Single cocktail loads

**Team:**
- [ ] Team page loads
- [ ] Images display
- [ ] Mobile layout works

**Homepage:**
- [ ] All sections load
- [ ] Images optimize
- [ ] No broken links
- [ ] Mobile menu works

**Navigation:**
- [ ] Main menu works
- [ ] Mobile menu works
- [ ] Footer links work

**General:**
- [ ] No PHP errors
- [ ] No JavaScript errors
- [ ] HTTPS working (on production)
- [ ] Caching configured (on production)

---

## 🔧 Useful Commands

### Container Management

```bash
# Start containers
docker compose up -d

# Stop containers
docker compose down

# View logs
docker compose logs wordpress    # WordPress logs
docker compose logs mysql        # MySQL logs

# Rebuild containers
docker compose up -d --build

# Access WordPress container
docker compose exec wordpress bash

# Access MySQL
docker compose exec mysql mysql -u wordpress -p
# Enter password: wordpress
```

### Frontend Development

```bash
# Watch for changes (auto-rebuild)
npm run watch

# Dev server (only frontend assets)
npm run dev

# Build for production
npm run build
```

### Database

**PhpMyAdmin:** http://localhost:8081
- Server: `mysql`
- Username: `wordpress`
- Password: `wordpress`

**Commands:**
```bash
# Export database
docker compose exec mysql mysqldump -u wordpress -pwordpress wordpress_db > backup.sql

# Import database
docker compose exec -T mysql mysql -u wordpress -pwordpress wordpress_db < backup.sql
```

---

## 📋 Cleanup & Reset

### Remove All Local Data

```bash
# Stop containers and remove volumes
docker compose down -v

# This deletes:
# - All container data
# - MySQL database
# - WordPress files

# To start fresh:
docker compose up -d
```

### Reset Database Only

```bash
docker compose exec mysql mysql -u root -prootpassword -e "DROP DATABASE wordpress_db; CREATE DATABASE wordpress_db;"
docker compose restart wordpress
```

---

## ⚠️ Common Issues & Solutions

### Issue: WordPress Installation Page Appears

**Solution:**
1. Containers still initializing (wait 60 seconds)
2. Or manually create database:
   ```bash
   docker compose exec mysql mysql -u root -prootpassword -e "CREATE DATABASE IF NOT EXISTS wordpress_db; GRANT ALL ON wordpress_db.* TO 'wordpress'@'%';"
   ```

### Issue: Can't Connect to Database

**Solution:**
1. Verify MySQL is healthy: `docker compose ps`
2. Check credentials in `.env` file match `docker-compose.yml`
3. Containers may need restart:
   ```bash
   docker compose down
   docker compose up -d
   ```

### Issue: Calculator Doesn't Submit

**Solution:**
1. Check browser console (F12) for JavaScript errors
2. Verify SMTP is configured (or emails fail silently)
3. Check WordPress debug log: `wp-content/debug.log`
4. Check nonce is valid in page source

### Issue: Vite Build Fails

**Solution:**
```bash
# Clear node_modules and reinstall
rm -r node_modules package-lock.json
npm install
npm run build
```

### Issue: Too Slow / Performance Issues

**Solution:**
1. Increase Docker memory: Settings → Resources
2. Disable certain plugins temporarily
3. Use `npm run build` instead of dev mode

---

## 📝 Notes

- **Local development** uses Docker for easy setup
- **Production deployment** may use Docker or traditional hosting
- **Vite build** required before going live
- **ACF Pro** requires valid license
- **SMTP** required for working email (production)
- **Database backups** recommended before major changes

---

**Status: Ready for local testing** ✓
