
# 🐶 Puppy DataTable (Laravel Package)

A lightweight Laravel backend DataTable package that makes it easy to build paginated, searchable, and sortable API responses for your frontend tables.
Unlike heavy alternatives, Puppy DataTable is simple, fast, and dependency-free.

## 🚀 Features

- ✅ **Lightweight** – no jQuery or heavy frontend dependencies
- ✅ **Works with any Eloquent model or query builder**
- ✅ **Built-in support for search, sort, and pagination**
- ✅ **Add custom computed columns easily**
- ✅ **API-friendly JSON responses**

## 📝 Front-End Package

```bash
https://www.npmjs.com/package/puppy-data-table
```


## 📦 Installation

**Require the package via Composer:**

```bash
composer require dilum/puppy-datatable
```

## ⚡ Usage Example

**Controller Example**
```bash
use Dilum\PuppyDataTable\DataTable;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return DataTable::of(User::query())
            ->searchable(['name', 'email'])
            ->addColumn('full_name', function ($user) {
                return trim($user->first_name . ' ' . $user->last_name);
            })
            ->editColumn('mobile_number', function ($user) {
                return $user->c_code . $user->mobile_number;
            })
            ->orderColumn('created_at', function ($query, $dir) {
                $query->orderBy('created_at', $dir);
            })
            ->toResponse($request);
    }
}
```

## 📤 Example API Response

```bash
{
  "data": [
    {"id":1, "name":"John", "email":"john@example.com", "full_name":"John Doe"},
    {"id":2, "name":"Jane", "email":"jane@example.com", "full_name":"Jane Smith"}
  ],
  "meta": {
    "total": 50,
    "page": 1,
    "per_page": 10
  }
}
```

## 🛠️ Methods

| Method                         | Description                                              |
| ------------------------------ | -------------------------------------------------------- |
| `of($query)`                   | Bind the DataTable to an Eloquent query or query builder |
| `searchable(array)`            | Define which columns are searchable                      |
| `addColumn($key, $callback)`   | Add custom computed column 
| `editColumn($key, $callback)` | Edit custom computed column                           |
| `orderColumn($key, $callback)` | Define custom ordering for a column                      |
| `toResponse($request)`         | Return API-ready JSON response                           |



## 🎯 Example with Custom Columns

```bash
return DataTable::of(User::query())
    ->searchable(['name', 'email'])
    ->addColumn('status', function ($user) {
        return $user->is_active ? 'Active' : 'Inactive';
    })
    ->toResponse(request());
```

## 📄 License

**MIT License** – free for personal and commercial use.

