# ⚡ Quick Start - Appointifi UI Refactor

## 🎯 What You Have Now

✅ Modern, responsive UI with Tailwind CSS
✅ Livewire-powered sidebar (no custom JavaScript)
✅ Dark mode toggle
✅ Role-based navigation (Owner vs Customer)
✅ Mobile-friendly with hamburger menu
✅ Styled login/register pages
✅ Clean landing page

---

## 🚀 3-Step Installation

### Step 1: Run Installation Script
**Windows (Double-click):**
```
install-ui-refactor.bat
```

**OR Laragon Terminal (Manual):**
```bash
composer require livewire/livewire
npm install
npm run dev
```

> ⚠️ **Keep `npm run dev` running!** It's needed for Vite hot-reloading.

---

### Step 2: Start Laravel Server
**New terminal window:**
```bash
php artisan serve
```

---

### Step 3: Test in Browser
```
http://localhost:8000
```

---

## ✅ Quick Testing Checklist

### Landing Page (/)
- [ ] Modern design with "Appointifi" branding
- [ ] Login/Sign Up buttons work
- [ ] Responsive on mobile

### Login (/login)
- [ ] Card design with gradient background
- [ ] Sign up link at bottom works

### Register (/register)
- [ ] Role dropdown (Customer / Business Owner)
- [ ] Register button works

### Owner Dashboard (after login as owner)
- [ ] Sidebar visible with 5 links
- [ ] Dark mode toggle works
- [ ] Mobile menu works (< 768px width)
- [ ] All navigation links work

### Customer Dashboard (after login as customer)
- [ ] Different sidebar links (3 links)
- [ ] Dark mode persists
- [ ] Mobile menu works

---

## 📁 Key Files

```
resources/views/
├── layouts/
│   ├── app.blade.php          ← Authenticated layout
│   └── guest.blade.php        ← Public layout
├── livewire/
│   └── sidebar.blade.php      ← Sidebar component
├── auth/
│   ├── login.blade.php        ← Styled login
│   └── register.blade.php     ← Styled register
└── welcome.blade.php          ← Landing page

app/Http/Livewire/
└── Sidebar.php                ← Sidebar logic

tailwind.config.js             ← Theme config
```

---

## 🎨 Customization Tips

### Change Primary Color
Edit `tailwind.config.js`:
```js
colors: {
  primary: {
    // Change these values
    500: '#3b82f6',  // Blue instead of red
    600: '#2563eb',
    // etc.
  }
}
```

### Add New Sidebar Link
Edit `resources/views/livewire/sidebar.blade.php`:
```blade
<a href="{{ route('your.route') }}" class="flex items-center gap-3 px-4 py-3 ...">
    <svg><!-- icon --></svg>
    <span>Your Link</span>
</a>
```

---

## 🐛 Common Issues

**Sidebar not showing?**
```bash
composer require livewire/livewire
php artisan view:clear
```

**Styles broken?**
```bash
npm install
npm run dev  # Keep running!
```

**Dark mode not working?**
- Check browser console (F12)
- Clear cache: Ctrl+Shift+R

---

## 📚 Full Documentation

- **Complete Guide:** `UI_REFACTOR_GUIDE.md`
- **Testing Checklist:** `TESTING_GUIDE.md`
- **This Quick Start:** `QUICK_START.md`

---

## 🎓 Next Steps

1. ✅ Complete installation (above)
2. ✅ Test all features (TESTING_GUIDE.md)
3. 🎨 Customize colors/branding
4. 📄 Add FluxUI components (https://fluxui.dev)
5. 🚀 Build your app features!

---

**Ready to start?** Run `install-ui-refactor.bat` now! 🚀
