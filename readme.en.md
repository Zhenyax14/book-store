🇪🇸 [Español](README.md) | 🇬🇧 [English](README.en.md)

# 📚 BookShop - Book Sales and Subscription Platform

BookShop is a web platform that allows users to purchase digital books, subscribe to access a reading library, and manage their profile and orders.

The project also includes an administration panel to manage books and the store catalog.

Developed with Laravel + Vue.js, applying SOLID principles and Agile methodologies during development, and securely deployed using Docker at:

https://booksplanet.store

# 🚀 Features
## 👤 Users

- User registration and authentication
- Profile management
- View previous orders
- Purchase individual books
- Monthly/annual subscription for library access
- Read books available through subscription

## 📚 Book Store

- Book catalog
- Individual book detail pages
- Purchase books individually
- Access books included in subscription
- Online reading via subscription
- Stripe payment integration

## 💳 Payments

Integration with Stripe for:

- Individual book purchases
- Subscription payments
- Payment status management

## 📦 Order Management

The system will store:

- Orders placed by users
- Purchase history
- Order status
- Books included in each order

---     
## 🔑 Administration Panel

Administrators can:

- Create books
- Edit books
- Delete books
- Manage the catalog
- View orders

### Book CRUD
Main fields:

- id
- title
- slug
- description
- isbn
- language
- publisher
- published_year
- edition
- pages_count
- cover_path
- access_type (ej. subsciption, individual, etc)
- price
- status (ej draft, published, etc)
- published_at
- timestamps
- softDeletes
---
### Use Case Diagram

![Diagrama use-case](./docs/img/use-case/use-case.png)

## 🏗 System Architecture
## 🧱 Project Architecture

The project follows a modern architecture based on:

- **Backend API**: Laravel
- **Public Frontend**: Server Side Rendering (SSR)
- **Administration Panel**: Client Side Rendering (CSR) with Vue.js
- **Database**: PostgreSQL
- **Containers**: Docker

**Simplified structure:**
```bash
bookshop/
│
├── book-store-backend
├── book-store-frontend
├── book-store-docker
├── docs
└── README.md
````
## ⚙️ Rendering Strategy

The platform uses a **hybrid rendering architecture** to optimize SEO, performance, and interactivity.

### Server Side Rendering (SSR)

Used in the public part of the platform:

- Book catalog
- Book detail pages
- Landing pages
- Indexable content

**Advantages:**

- Better SEO
- Faster initial rendering
- Better search engine indexing

### Client Side Rendering (CSR)

Used in:

- Administration panel
- Interactive book reader

**Examples:**

- Book management (CRUD)
- Catalog management
- System administration

**Advantages:**

- SPA-like experience
- Faster interactions
- Reduced page reloads
- Does not require strong SEO optimization
# 🛠 Technologies Used

## Backend

- Laravel 12
- PHP 8+
- Eloquent ORM
- Laravel Authentication
- Laravel Policies / Middleware
- FormRequests
- Laravel Sanctum

## Frontend

👉 **[See design mockup](https://zhenyax14.github.io/book_shop/design/concept-art.html)**

- Vue.js 3
- Inertia.js
- Server Side Rendering (SSR) (Public section)
- Client Side Rendering (CSR) (Admin panel, book reader)
- Vite
- HTML5
- CSS3
- Bootstrap and SCSS

## Payments

- Stripe
- Stripe Webhooks

## Infrastructure

- Docker
- Docker Compose
- Nginx
- PostgreSQL

## SEO

- Google Search Console
- Google Analytics
- HTML Optimization

## Hosting

The project is deployed at:

https://booksplanet.store

Infrastructure:

- Linux VPS
- Docker
- Nginx
- PostgreSQL

# 📋 Technical Requirements

To run the project you need:

- Docker
- Docker Compose
- Git

Optional (if running without Docker):

- PHP 8.2+
- Composer
- Node.js 18+
- npm or pnpm
- PostgreSQL
# 🐳 Installation with Docker

## 1. Clone the repository

```bash
git clone https://github.com/zhenyax14/book_shop.git
cd book_shop
```

## 2. Copy the environment
Copy the default environment
```bash
  cp .env.example .env
```

edit the file
```bash
   nano .env
```

Add your DEV or PROD credentials:
```php
   DB_DATABASE=bookshop
   DB_USERNAME=root
   DB_PASSWORD=root
   
   STRIPE_KEY=your_key
   STRIPE_SECRET=your_secret
```

## 3. Start the containers

```bash
   docker compose up -d
```

## 4. Install dependencies
```bash
   docker compose exec app composer install
   docker compose exec app npm install
   docker compose exec app npm run dev
```

## 5. Generate app key
```bash
  docker compose exec app php artisan key:generate
```

## 6. Run migrations

```bash
  docker compose exec app php artisan migrate
```

# 📊 Data Model

![E-R Diagram](./docs/img/ER/er-diagram.png)

Main system entities:

### Users

- id
- name
- email
- email_verified_at
- password
- role
- remember_token
- timestamps

### Books

- id
- title
- slug
- description
- isbn
- language
- publisher
- published_year
- edition
- pages_count
- cover_path
- access_type (e.g. subscription, individual, etc)
- price
- status (e.g. draft, published, etc)
- published_at
- timestamps
- softDeletes

### Tags

- id
- name
- slug
- color
- created_at
- updated_at

### Book Tags (Pivot Table)

- book_id
- tag_id

### Book Volumes

- id
- book_id
- number
- title
- timestamps

### Book Chapters

- id
- book_id
- volume_id
- number
- title
- slug
- reading_time_minutes
- is_published
- timestamps

### Book Pages

- id
- chapter_id
- number
- global_number
- content
- content_format
- word_count
- timestamps

### User Reading Progress

- id
- user_id
- book_id
- chapter_id
- page_id
- scroll_position
- completion_percentage
- last_read_at
- is_finished
- finished_at
- timestamps

### Bookmarks

- id
- user_id
- book_id
- page_id
- label
- color
- timestamps

### Annotations

- id
- user_id
- page_id
- start_offset
- end_offset
- selected_text
- color
- note
- timestamps

### Reading Sessions

- id
- user_id
- book_id
- start_page_id
- end_page_id
- started_at
- ended_at
- pages_read
- duration_seconds
- timestamps

### Orders

- id
- user_id
- total
- payment_status
- stripe_session_id

### Order Items

- id
- order_id
- book_id
- price

### Subscriptions

- id
- user_id
- stripe_subscription_id
- status
- expires_at

### Reading Settings

- id
- user_id
- font_size
- font_family
- line_height
- theme
- page_width
- pagination_mode
- words_per_page
- timestamps

![UML Diagram](./docs/img/ER/e-diagram.png)


# 🔐 Security

## Back-End

- CSRF protection
- Validation using FormRequest
- Laravel Sanctum for APIs
- Role-based route protection

## Front-End

- Live form validation
- Communication with Back-End via token

## Deployment

- Basic VPS server hardening (ufw, strong passwords, ssh)
- Correct folder permissions according to structure

# 📖 Project Roadmap

Possible future improvements:

- Book review system
- Favorites / wishlist
- Book recommendations
- Admin metrics dashboard
- AI assistant

# 🧪 Testing

Planned testing strategy:

- Unit tests with PHPUnit
- API integration tests
- Simulated payment tests with Stripe

# 📄 License

This project is proprietary software.
All rights reserved. No part of this software may be copied, modified,
distributed, or used without express written permission from the author.

# 📖 Additional Documentation

[Stripe](https://docs.stripe.com/ )

[Laravel](https://laravel.com/docs/12.x/installation)

[Vue](https://vuejs.org/guide/introduction.html)
