// invoice-generator/scripts/image-helpers.js
// Client-side image helpers used by the logo upload and (in Phase B) any
// other image input the tool ecosystem accepts. Kept dependency-free.
//
// IMAGE CONVENTIONS for the invoice-generator and any niche/template pages:
//
//   1. Every <img> must include width and height attributes (numeric, no
//      units). Prevents Cumulative Layout Shift during hydration.
//   2. Below-the-fold images use loading="lazy". Above-the-fold images use
//      loading="eager" and (where it materially helps LCP) fetchpriority="high".
//   3. Decorative images use empty alt="". Content images use descriptive alt.
//   4. Where multiple formats are available, use <picture> with WebP first
//      and PNG / JPEG fallback.
//   5. User-uploaded images (the logo) are resized client-side before storage
//      to stay under localStorage quota. See resizeImageDataUrl below.

export function resizeImageDataUrl(file, maxWidth = 800, maxHeight = 800, quality = 0.85) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onerror = () => reject(reader.error || new Error('File read failed'));
    reader.onload = (e) => {
      const img = new Image();
      img.onerror = () => reject(new Error('Image load failed'));
      img.onload = () => {
        const ratio = Math.min(maxWidth / img.width, maxHeight / img.height, 1);
        const w = Math.round(img.width * ratio);
        const h = Math.round(img.height * ratio);
        const canvas = document.createElement('canvas');
        canvas.width = w;
        canvas.height = h;
        canvas.getContext('2d').drawImage(img, 0, 0, w, h);
        const mime = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
        resolve({
          dataUrl: canvas.toDataURL(mime, quality),
          width: w,
          height: h,
        });
      };
      img.src = e.target.result;
    };
    reader.readAsDataURL(file);
  });
}
