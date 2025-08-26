# puppy-data-table

Lightweight Laravel DataTable with Yajra-like syntax and a vanilla JS frontend.

## Installation (local development)
1. Copy this package into `packages/dilum/puppy-data-table` inside your Laravel app.
2. Add to your app `composer.json` repositories:
   ```json
   "repositories": [
     { "type": "path", "url": "packages/dilum/puppy-data-table" }
   ]
   ```
3. Require the package:
   ```
   composer require dilum/puppy-data-table:@dev
   ```
4. Publish assets:
   ```
   php artisan vendor:publish --tag=puppy-datatable-assets
   ```

## Usage
See example Controller in `src/Examples/PartnerController.php`.

## Features
- addColumn / editColumn
- filterColumn / orderColumn
- rawColumns (allow HTML)
- searchable([...])
- export(csv|excel)
- server-side relation handling (with)
