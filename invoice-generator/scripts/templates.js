// invoice-generator/scripts/templates.js
// Template style registry + applier. Styles are pure CSS variable swaps driven by [data-template] on the body.

// Five template styles mirroring the Argo Books desktop app's invoice
// templates so the web tool and the app feel like the same product. Each
// id ties to a [data-template="<id>"] CSS scope in tool.css and a per-id
// branch in scripts/docx.js.
export const TEMPLATES = [
  { id: 'classic', name: 'Classic', description: 'Traditional and conservative.' },
  { id: 'modern', name: 'Modern', description: 'Clean sans-serif, accent color.' },
  { id: 'formal', name: 'Formal', description: 'Stripped down, lots of whitespace.' },
  { id: 'elegant', name: 'Elegant', description: 'Yellow accent header, high contrast.' },
  { id: 'ribbon', name: 'Ribbon', description: 'Serif headings with a thin accent rule.' },
];

export function applyTemplate(templateId) {
  const valid = TEMPLATES.find(t => t.id === templateId);
  if (!valid) return null;
  document.body.setAttribute('data-template', templateId);
  // Mirror on the invoice-app wrapper so static-rendered pages without JS get a sane default too.
  const wrapper = document.querySelector('.invoice-app');
  if (wrapper) wrapper.setAttribute('data-template', templateId);
  return valid;
}
