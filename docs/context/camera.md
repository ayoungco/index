# Browser Camera Access in Web Apps

## What is Allowed

A Laravel app (or any web app) can prompt the phone camera if all of these are true:

### ✅ 1. User Interaction

The camera must be triggered by a user action, like:
- Tapping a button
- Clicking an input field

❌ You cannot auto-open the camera on page load.

### ✅ 2. HTTPS (Required)

Mobile browsers require HTTPS for camera access.
- `https://yourapp.com` → ✅ works
- `http://yourapp.com` → ❌ blocked

(Localhost is the only exception.)

### ✅ 3. Supported Browser APIs

You have two common, reliable options:

#### Option A: `<input type="file" capture>` (Simplest, Most Compatible)

This works extremely well on iOS and Android.

```html
<input
    type="file"
    accept="image/*"
    capture="environment"
/>
```

**Behavior:**
- iPhone → opens camera directly
- Android → camera or camera chooser
- Desktop → file picker

**Advantages:**
- ✔ No JS required
- ✔ Safari-compatible
- ✔ Very reliable

Use this if you just need a photo upload.

#### Option B: `getUserMedia()` (Full Camera Control)

```javascript
navigator.mediaDevices.getUserMedia({ video: true })
```

**Pros:**
- Live preview
- Video capture
- Barcode / QR scanning
- Advanced workflows

**Cons:**
- More code
- Safari quirks
- Still requires a user click

**Advantages:**
- ✔ Works on modern mobile browsers
- ✔ Requires permission prompt

---

## What is NOT Allowed

- ❌ Auto-launching camera on page load
- ❌ Bypassing the permission prompt
- ❌ Using camera on HTTP
- ❌ Forcing background camera access

Browsers enforce this — Laravel can't override it.

---

## Laravel-Specific Note

Laravel does not control camera access — it only:
- Serves the page
- Receives the uploaded image/video
- Processes/stores it

All camera permissions happen entirely in the browser.

---

## Recommended Pattern

For mobile-first workflows (field photos, inspections, uploads):

```html
<form method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="photo" accept="image/*" capture="environment">
    <button type="submit">Upload</button>
</form>
```

**This:**
- Opens the phone camera
- Works on iOS Safari (the hardest case)
- Requires zero JS
- Survives browser updates

---

## Quick Decision Guide

- 📸 Photo upload only → use `<input capture>`
- 🎥 Live preview / scanning → use `getUserMedia()`
- 🧾 QR / barcode scanning → use a lightweight JS library
- 📱 iOS edge cases → use Safari-safe markup