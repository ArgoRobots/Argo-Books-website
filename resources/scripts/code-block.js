// resources/scripts/code-block.js
//
// Copy button and language tabs for the code blocks rendered by
// partials/code-block.php. Shared by the API documentation and the guides,
// so it lives here rather than inside either page family's own script.
//

/**
 * Copy button on documentation code blocks.
 *
 * The source is read from the block's data-code attribute rather than from the
 * rendered text, so the syntax-highlighting markup can never end up in what the
 * reader pastes. Delegated from the document so it also covers blocks that are
 * added after load.
 */
document.addEventListener("click", async (event) => {
  const button = event.target.closest(".code-copy");
  if (!button) return;

  const block = button.closest(".code-block");
  if (!block) return;

  const label = button.querySelector(".code-copy-text");

  // A tabbed block keeps the source on each variant, so copy whichever one the
  // reader is actually looking at rather than the block as a whole.
  const active = block.querySelector(".code-variant.is-active");
  const source = (active || block).getAttribute("data-code") || "";

  const show = (text) => {
    if (label) label.textContent = text;
    button.classList.add("is-copied");
    setTimeout(() => {
      if (label) label.textContent = "Copy";
      button.classList.remove("is-copied");
    }, 1600);
  };

  try {
    await navigator.clipboard.writeText(source);
    show("Copied");
  } catch {
    // Clipboard access is refused on insecure origins and in some browsers, so
    // fall back to the old selection trick rather than leaving the button dead.
    const scratch = document.createElement("textarea");
    scratch.value = source;
    scratch.setAttribute("readonly", "");
    scratch.style.position = "fixed";
    scratch.style.opacity = "0";
    document.body.appendChild(scratch);
    scratch.select();
    try {
      document.execCommand("copy");
      show("Copied");
    } catch {
      show("Press Ctrl+C");
    }
    document.body.removeChild(scratch);
  }
});

/**
 * Language tabs on documentation code blocks.
 *
 * The choice is remembered and applied to every block on every page, because a
 * C# developer should pick C# once rather than on each example. Blocks that do
 * not offer the remembered language keep their own first tab rather than
 * showing nothing.
 */
(() => {
  const STORAGE_KEY = "argoDocsCodeLang";

  const selectVariant = (block, variant) => {
    const panes = block.querySelectorAll(".code-variant");
    const tabs = block.querySelectorAll(".code-tab");
    let matched = false;

    panes.forEach((pane) => {
      const isMatch = pane.dataset.variant === variant;
      pane.classList.toggle("is-active", isMatch);
      if (isMatch) matched = true;
    });

    tabs.forEach((tab) => {
      const isMatch = tab.dataset.variant === variant;
      tab.classList.toggle("is-active", isMatch);
      tab.setAttribute("aria-selected", isMatch ? "true" : "false");
    });

    return matched;
  };

  const applyStoredLanguage = () => {
    let stored = null;
    try {
      stored = localStorage.getItem(STORAGE_KEY);
    } catch {
      return; // storage blocked; the server-rendered default stands
    }
    if (!stored) return;

    document.querySelectorAll(".code-block-tabbed").forEach((block) => {
      // Leave the block on its own default when it has no such variant.
      if (!block.querySelector(`.code-variant[data-variant="${CSS.escape(stored)}"]`)) return;
      selectVariant(block, stored);
    });
  };

  document.addEventListener("click", (event) => {
    const tab = event.target.closest(".code-tab");
    if (!tab) return;

    const block = tab.closest(".code-block-tabbed");
    if (!block) return;

    const variant = tab.dataset.variant;
    selectVariant(block, variant);

    // Every other block follows along, so scrolling the page does not mean
    // re-picking the language over and over.
    document.querySelectorAll(".code-block-tabbed").forEach((other) => {
      if (other !== block) selectVariant(other, variant);
    });

    try {
      localStorage.setItem(STORAGE_KEY, variant);
    } catch {
      // Not being able to remember the choice is not worth surfacing.
    }
  });

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", applyStoredLanguage);
  } else {
    applyStoredLanguage();
  }
})();
