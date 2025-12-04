# Al-Matar Al-Thari - Digital Loyalty Platform

A complete responsive static website for Al-Matar Al-Thari, a digital loyalty, discount, and affiliate marketing system.

## 🌟 Features

- **Multi-language Support**: Full English (EN) and Arabic (AR) support with RTL layout
- **Responsive Design**: Works perfectly on all devices (desktop, tablet, mobile)
- **Modern UI**: Clean, modern design with smooth animations
- **Bootstrap 5**: Latest Bootstrap framework with custom styling
- **jQuery**: Enhanced interactivity and animations
- **FontAwesome Icons**: Beautiful iconography throughout
- **Form Validation**: Client-side validation for all forms
- **FAQ Accordion**: Interactive FAQ section
- **Carousel**: Featured offers carousel
- **Smooth Scrolling**: Enhanced user experience

## 📁 Project Structure

```
/
├── index.html              # Home page
├── about.html              # About Us page
├── how-it-works.html       # How It Works page
├── features.html            # Features page
├── contact.html             # Contact page
├── faq.html                 # FAQ page
├── login.html               # Login page
├── register.html            # Registration page
├── assets/
│   ├── css/
│   │   ├── styles.css      # Main stylesheet
│   │   └── rtl.css          # RTL-specific styles
│   ├── js/
│   │   ├── main.js         # Main JavaScript file
│   │   └── lang.js         # Language switching system
│   └── lang/
│       ├── en.json         # English translations
│       └── ar.json         # Arabic translations
└── README.md
```

## 🚀 Getting Started

1. **Clone or Download** this repository
2. **Open** `index.html` in your web browser
3. **No build process required** - it's a static website!

## 🌍 Language Switching

The website supports two languages:
- **English (EN)**: Left-to-right (LTR) layout
- **Arabic (AR)**: Right-to-left (RTL) layout

Users can switch languages using the language switcher in the navigation bar. The language preference is saved in localStorage.

## 📄 Pages

### Home (index.html)
- Hero section with call-to-action
- System explanation
- How it works (for customers and merchants)
- Featured companies
- Featured offers carousel
- Call-to-action section

### About (about.html)
- Vision and mission
- Company values
- Why choose us
- Timeline/journey

### How It Works (how-it-works.html)
- Step-by-step guide for customers
- Step-by-step guide for merchants
- QR code usage explanation
- Loyalty points system
- Affiliate program

### Features (features.html)
- Admin features
- Merchant features
- Customer features
- Additional features

### Contact (contact.html)
- Contact form with validation
- Contact information boxes
- Google Maps placeholder (ready for integration)

### FAQ (faq.html)
- Interactive accordion FAQ section
- Common questions and answers

### Login (login.html)
- Login form with validation
- Social login options (UI only)

### Register (register.html)
- Registration form with all required fields
- Password confirmation
- Terms and conditions checkbox
- Social registration options (UI only)

## 🎨 Design

- **Primary Color**: #3D4F60
- **Success Color**: #4BB543
- **Info Color**: #17A2B8
- **Modern UI**: Rounded corners, smooth animations, hover effects
- **Responsive**: Mobile-first approach

## 🔧 Technologies Used

- HTML5
- CSS3 (Custom + Bootstrap 5)
- JavaScript (Vanilla JS + jQuery)
- Bootstrap 5.3.0
- jQuery 3.7.0
- FontAwesome 6.4.0

## 📝 Form Validation

All forms include client-side validation:
- Required field validation
- Email format validation
- Password strength (minimum 6 characters)
- Password confirmation matching
- Real-time error messages

## 🎯 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 📱 Responsive Breakpoints

- Mobile: < 576px
- Tablet: 576px - 768px
- Desktop: > 768px

## 🔐 Security Notes

This is a static website with no backend. For production use:
- Implement server-side validation
- Add CSRF protection
- Use HTTPS
- Implement proper authentication
- Add rate limiting for forms

## 📧 Contact Information

- Email: info@almatar.com
- Phone: +123 456 7890
- Address: 123 Business Street, City, Country

## 📄 License

© 2024 Al-Matar Al-Thari. All rights reserved.

## 🛠️ Customization

### Adding New Languages

1. Create a new JSON file in `assets/lang/` (e.g., `fr.json`)
2. Copy the structure from `en.json`
3. Translate all values
4. Update `lang.js` to include the new language

### Modifying Colors

Edit CSS variables in `assets/css/styles.css`:
```css
:root {
  --primary-color: #3D4F60;
  --success-color: #4BB543;
  --info-color: #17A2B8;
}
```

### Adding Google Maps

Replace the placeholder in `contact.html` with your Google Maps embed code:
```html
<iframe src="YOUR_GOOGLE_MAPS_EMBED_URL" width="100%" height="400" style="border:0;"></iframe>
```

## ✨ Features Implemented

✅ Multi-language support (EN/AR)
✅ RTL layout for Arabic
✅ Responsive design
✅ Form validation
✅ FAQ accordion
✅ Carousel for offers
✅ Smooth scrolling
✅ Scroll-to-top button
✅ Mobile menu
✅ Sticky navbar
✅ Animations
✅ Modern UI/UX
✅ All required pages
✅ Production-ready code

---

**Built with ❤️ for Al-Matar Al-Thari**

