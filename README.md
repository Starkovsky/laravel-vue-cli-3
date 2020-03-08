## Laravel 7 + Vue CLI 4
Example config your Laravel project with two builds (public and admin)

## Steps for Scaffolding From Scratch
1. Create Laravel Project

   ``` sh
   laravel new my-project
   cd my-project
   # remove existing frontend scaffold
   rm -rf package.json webpack.mix.js yarn.lock resources/js resources/sass public/js public/css
   ```

2. Create a Vue CLI 4 project in the Laravel '/resources/frontend/'
   ``` sh
   cd resources/frontend
   vue create app
   #and (if you need admin build)
   vue create admin
   ```

3. Configure Vue CLI 4 project

    Create `/resources/frontend/app/vue.config.js`:

    ``` js
    module.exports = {
     devServer: {
       proxy: 'http://laravel.test'
     },

      // output built static files to Laravel's public dir.
      // note the "build" script in package.json needs to be modified as well.
      outputDir: '../../../public/assets/app',

      publicPath: process.env.NODE_ENV === 'production'
        ? '/assets/app/'
        : '/',

      // modify the location of the generated HTML file.
      indexPath: process.env.NODE_ENV === 'production'
        ? '../../../resources/views/app.blade.php'
        : 'index.html'
    }
    ```
    Edit `/resources/frontend/app/package.json`
    ``` diff
    "scripts": {
      "serve": "vue-cli-service serve",
    - "build": "vue-cli-service build",
    + "build": "rm -rf ../../../public/assets/app/{js,css,img} && vue-cli-service build --no-clean",
      "lint": "vue-cli-service lint"
    },
    ```
    # and (if you need admin build)

    Create `/resources/frontend/admin/vue.config.js`:
    ```javascript
    module.exports = {
      // proxy API requests to Valet during development
      devServer: {
        proxy: 'http://laravel.test/admin'
      },

      // output built static files to Laravel's public dir.
      // note the "build" script in package.json needs to be modified as well.
      outputDir: '../../../public/assets/admin',

      publicPath: process.env.NODE_ENV === 'production'
        ? '/assets/admin/'
        : '/admin',

      // modify the location of the generated HTML file.
      // make sure to do this only in production.
      indexPath: process.env.NODE_ENV === 'production'
        ? '../../../resources/views/admin.blade.php'
        : 'index.html'
    }
    ```
   
    Edit `/resources/frontend/admin/package.json`
    ``` diff
    "scripts": {
      "serve": "vue-cli-service serve",
    - "build": "vue-cli-service build",
    + "build": "rm -rf ../../../public/assets/admin/{js,css,img} && vue-cli-service build --no-clean",
      "lint": "vue-cli-service lint"
    },
    ```
4. Configure Laravel routes for SPA.

    **routes/web.php**

    ``` php
    <?php
    // For admin application
    Route::get('/admin{any}', 'FrontendController@admin')->where('any', '.*');
    // For public application
    Route::any('/{any}', 'FrontendController@app')->where('any', '^(?!api).*$');
    ```

    **app/Http/Controllers/FrontendController.php**

    ``` php
    <?php
    namespace App\Http\Controllers;
    use Illuminate\Http\Request;

    class FrontendController extends Controller
    {
        // For admin application
        public function admin()
        {
            return view('admin');
        }
        // For public application
        public function app()
        {
            return view('app');
        }
    }
    ```
5. Change `base: process.env.BASE_URL` in `router.js` for correct Vue Router
    ``` js
    // For App
    base: '/',
    // For Admin
    base: '/admin/',
    ```
6. Add `package.json` in root (if you want use `yarn run` in root)
    ``` js
    {
      "name": "laravel",
      "version": "0.2.0",
      "private": true,
      "scripts": {
        // For public application
        "prepare:app": "cd resources/frontend/app && yarn install",
        "serve:app": "cd resources/frontend/app && yarn run serve",
        "build:app": "cd resources/frontend/app && yarn run build",
        "lint:app": "cd resources/frontend/app && yarn run lint",
        "test:app": "cd resources/frontend/app && yarn run test:unit",
        // For admin application
        "prepare:admin": "cd resources/frontend/admin && yarn install",
        "serve:admin": "cd resources/frontend/admin && yarn run serve",
        "build:admin": "cd resources/frontend/admin && yarn run build",
        "lint:admin": "cd resources/frontend/admin && yarn run lint",
        "test:admin": "cd resources/frontend/admin && yarn run test:unit"
      }
    }
    ```
