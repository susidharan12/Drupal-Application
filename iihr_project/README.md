# IIHR Website - Drupal 11 + HTMX

## Project Structure
```
iihr_project/
├── docker-compose.yml
├── Dockerfile
└── Drupal/
    ├── modules/custom/iihr_module/     ← Custom module (routes + templates)
    │   ├── iihr_module.info.yml
    │   ├── iihr_module.module
    │   ├── iihr_module.routing.yml
    │   ├── src/Controller/IihrController.php
    │   └── templates/
    │       ├── iihr-home.html.twig     ← Home page
    │       └── iihr-about.html.twig   ← About page
    └── themes/custom/iihr_theme/      ← Custom theme
        ├── iihr_theme.info.yml
        ├── iihr_theme.libraries.yml
        ├── css/iihr.css
        ├── js/iihr.js
        ├── images/                    ← Add logo.png here
        └── templates/
            ├── html.html.twig
            └── page.html.twig
```

## Setup Steps

### Step 1: Start Docker
```bash
docker-compose up -d
```

### Step 2: Install Drupal
Visit http://localhost:8080 and complete Drupal installation:
- Choose **PostgreSQL** as database
- Host: `db`
- Database: `drupal`
- User: `drupal`
- Password: `drupalpass`

### Step 3: Copy files to container
```powershell
docker cp "Drupal/modules/custom/iihr_module" drupal__app:/var/www/html/modules/custom/
docker cp "Drupal/themes/custom/iihr_theme" drupal__app:/var/www/html/themes/custom/
```

### Step 4: Enable Theme
Go to: http://localhost:8080/admin/appearance
→ Find **IIHR Theme** → Click **Set as default**

### Step 5: Enable Module
Go to: http://localhost:8080/admin/modules
→ Find **IIHR Module** → Check it → Click **Install**

### Step 6: Set Front Page
Go to: http://localhost:8080/admin/config/system/site-information
→ Set **Default front page** to: `/home`
→ Click **Save**

### Step 7: Clear Cache
Go to: http://localhost:8080/admin/config/development/performance
→ Click **Clear all caches**

### Step 8: View Pages
- Home: http://localhost:8080/home
- About: http://localhost:8080/about

## Add Your Logo
Place your logo image at:
`Drupal/themes/custom/iihr_theme/images/logo.png`
Then run the docker cp command again for themes.

## pgAdmin (Database Manager)
- URL: http://localhost:8082
- Email: admin@admin.com
- Password: admin
