// invoice-generator/scripts/templates.js
// Template style registry + applier. Styles are pure CSS variable swaps driven by [data-template] on the body.

// Five template styles mirroring the Argo Books desktop app's invoice
// templates so the web tool and the app feel like the same product. Each
// id ties to a [data-template="<id>"] CSS scope in tool.css and a per-id
// branch in scripts/docx.js. The user-facing display name (`name`) is shown
// in the toolbar dropdown; some names diverge from their ids so the dropdown
// reads naturally (e.g. id="minimal" displays as "Formal").
export const TEMPLATES = [
  { id: 'classic', name: 'Classic', description: 'Clean default with a dark table header.' },
  { id: 'modern', name: 'Modern', description: 'Slate left rail, light header band, dark table header.' },
  { id: 'minimal', name: 'Formal', description: 'Georgia serif on a navy header and total band.' },
  { id: 'bold', name: 'Elegant', description: 'Multicolor gradient ribbon, indigo total due.' },
  { id: 'professional', name: 'Ribbon', description: 'Soft watercolor wave decoration along the left.' },
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
