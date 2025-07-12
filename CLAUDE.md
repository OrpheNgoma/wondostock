# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WondoStock is a multi-tenant inventory management system built with Laravel 12 and Livewire 3. It's designed for managing products, stock movements, sales, purchases, and multi-company operations with strict tenant isolation.

## Development Commands

### Primary Development
- `composer run dev` - Starts the full development environment (server, queue, logs, vite) using concurrently
- `php artisan serve` - Laravel development server only
- `npm run dev` - Vite development server for assets
- `npm run build` - Build production assets

### Testing
- `composer run test` - Run PHPUnit tests (clears config first)
- `php artisan test` - Direct test execution
- `vendor/bin/phpunit` - Manual PHPUnit execution

### Code Quality
- `vendor/bin/pint` - Laravel Pint code formatting
- `php artisan config:clear` - Clear configuration cache
- `php artisan migrate` - Run database migrations
- `php artisan db:seed` - Seed database with test data

### Queue and Background
- `php artisan queue:listen --tries=1` - Queue worker for development
- `php artisan pail --timeout=0` - Real-time log monitoring

## Architecture

### Multi-Tenancy System
The application implements strict tenant isolation through:

1. **CompanyScope** (`app/Scopes/CompanyScope.php`): Global Eloquent scope that automatically filters queries by company_id
2. **BelongsToCompany** trait (`app/Traits/BelongsToCompany.php`): Applied to models requiring tenant isolation
3. **TenantIsolation** middleware (`app/Http/Middleware/TenantIsolation.php`): Ensures users can only access their company's data
4. **SecureCompanyAccess** trait: Additional security layer for sensitive operations

### Core Models Structure
- **Company**: Main tenant entity
- **User**: Belongs to company, supports global admin role
- **Store**: Physical locations within a company
- **Product**: Inventory items with category, tax, unit relationships
- **Document**: Sales invoices, credit notes with configurable numbering
- **StockMovement**: Track all inventory changes
- **StockTransfer**: Inter-store transfers

### Livewire Components
All UI is built with Livewire 3 components organized by feature:
- `app/Livewire/Auth/` - Authentication
- `app/Livewire/Products/` - Product management
- `app/Livewire/Documents/` - Sales, invoices, credit notes
- `app/Livewire/Stock/` - Inventory movements and transfers
- `app/Livewire/Settings/` - Configuration and admin features

### Permission System
Uses Spatie Laravel Permission with company-scoped permissions. Permissions are isolated per company using the middleware system.

### Key Services
- **DocumentNumberService**: Generates sequential document numbers per company
- **MetricsService**: Analytics and reporting functionality

### PDF Generation
Uses barryvdh/laravel-dompdf for invoice and document generation.

### Barcode Support
Implements barcode generation using milon/barcode and picqer/php-barcode-generator for product labels.

## Development Guidelines

### Database Patterns
- All tenant-scoped models MUST use `BelongsToCompany` trait
- Always test multi-tenancy isolation when adding new features
- Use factories for testing with proper company_id assignment

### Security
- Never bypass CompanyScope without explicit need and security review
- Global admin routes are prefixed with `/global-admin`
- Always validate company ownership in controllers when accessing related models

### Frontend
- Use Tailwind CSS 4 for styling
- Livewire components follow feature-based organization
- Assets are built with Vite

### Testing
- Tests use SQLite in-memory database
- Company isolation must be tested for all tenant-scoped features
- Use factories to create test data with proper company relationships

## Stratégie de Branches Git

### Branches Principales
- **`main`**: Production stable - code déployé en production
- **`develop`**: Intégration continue - point de convergence pour toutes les fonctionnalités

### Branches de Fonctionnalités (`feature/`)
- **`feature/inventory-management`**: Gestion complète des stocks et mouvements
- **`feature/sales-invoicing`**: Système de vente et facturation 
- **`feature/purchase-management`**: Gestion des achats et fournisseurs
- **`feature/multi-tenant-security`**: Amélioration sécurité multi-tenant
- **`feature/reports-analytics`**: Rapports et analytics avancés
- **`feature/barcode-labels`**: Génération codes-barres et étiquettes
- **`feature/user-management`**: Gestion utilisateurs et permissions

### Branches d'Amélioration
- **`improvement/performance-optimization`**: Optimisations performance
- **`improvement/ui-ux-enhancement`**: Améliorations interface utilisateur
- **`enhancement/api-development`**: Développement API REST

### Branches de Maintenance
- **`bugfix/tenant-isolation`**: Corrections isolation tenant
- **`hotfix/production-issues`**: Corrections urgentes production
- **`release/v1.0.0`**: Préparation release v1.0.0

### Workflow Recommandé
1. Créer une branche feature depuis `develop`
2. Développer la fonctionnalité avec tests
3. Merger vers `develop` via Pull Request
4. Créer une branche `release/` depuis `develop` pour finaliser
5. Merger `release/` vers `main` et `develop`
6. Utiliser `hotfix/` depuis `main` pour corrections urgentes