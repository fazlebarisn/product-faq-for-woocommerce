=== Product FAQ for WooCommerce — Tab, Accordion & AI FAQ Generator ===
Contributors: fazlebari
Tags: woocommerce-faq, product-faq, faq, accordion, ai-faq
Requires at least: 5.0
Tested up to: 7.1
WC requires at least: 5.0
WC tested up to: 11.1.0
Requires PHP: 7.3
Stable tag: 1.6.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Boost WooCommerce conversions and Google Rich Results with Product FAQ tabs, Gutenberg blocks, rich HTML answers, JSON-LD schema & spec-aware AI FAQs.

== Description ==

**Boost eCommerce conversions, eliminate pre-sale hesitation, and rank higher on Google Search with Product FAQ for WooCommerce — engineered by [FBS Fusion](https://fbsfusion.com/).**

[youtube https://www.youtube.com/watch?v=AIVfoBu8XRg]

**Product FAQ for WooCommerce** lets you add fast, responsive, conversion-focused Frequently Asked Questions (FAQs) directly onto your WooCommerce product pages or anywhere across your store. Answer buyer objections right next to the Add to Cart button or inside a clean Product Tab—slashing repetitive support tickets and turning hesitant visitors into confident buyers.

= 🚀 What's New in Version 1.6.0 =

* **🧱 Native Gutenberg Block (`Product FAQ Accordion`)**: Build custom Single Product templates or standalone FAQ pages visually in the WordPress Block Editor & Full Site Editing (FSE). Just type `/faq` to insert and configure headings, Product IDs, or Category slugs with live preview!
* **✨ Rich HTML, Links & Multi-Paragraph Answers**: Add clickable links (size guides, shipping/return policy pages), **bold** & *italic* emphasis, bulleted lists (`<ul><li>`), and multi-paragraph answers safely sanitized with WordPress `wp_kses_post`.
* **🔢 Live Tab Count Badge (`FAQs (5)`) & Auto-Open First FAQ**: Automatically display a live question count badge on your WooCommerce Product Tab and optionally expand your #1 objection-buster FAQ on page load for instant visibility.
* **🤖 WooCommerce Spec-Aware AI Generation**: Connects seamlessly with WordPress's native AI Client (`Settings ➔ Connectors` for Google Gemini, OpenAI, or Anthropic) and automatically enriches prompts with real WooCommerce product metadata—**Price, Stock Status, Weight & Dimensions, Categories, and Product Attributes** (e.g., Material, Size, Color)—for accurate, factual product FAQs.
* **💾 1-Click JSON Backup, Export & Import**: Migrate or back up all your plugin design settings and Bulk Category/Tag FAQ groups between staging and production stores in one click.

= ✨ Core Free Features =
* **Spec-Aware AI FAQ Generator**: Generate product-specific FAQs in seconds with selectable copywriting tones (*Persuasive & Sales-Focused*, *Friendly*, *Concise*, *Technical*) plus built-in smart fallback templates even before connecting an AI provider.
* **Google `FAQPage` JSON-LD Schema**: Automatically outputs clean, tag-stripped Schema.org `FAQPage` structured data for Google Search rich results without requiring extra SEO plugins.
* **6 Flexible Display Positions**: Render FAQs inside **Product Tabs**, **After Cart Button**, **After Meta**, **After Summary**, **After Single Product**, or manually via the **Gutenberg Block** / **`[woo_product_faq]` Shortcode**.
* **3-Tier Category Hierarchy Engine**: Individual Product FAQs automatically take priority over Sub-Category Bulk FAQs, which take priority over Main Category Bulk FAQs.
* **1-Click Objection-Buster Templates**: Pre-built eCommerce templates for *Shipping & Delivery*, *Returns & Refunds*, *Warranty & Authenticity*, *Sizing & Fit*, and *Care Instructions*.
* **WooCommerce HPOS & Cart/Checkout Blocks Compatible**: 100% compatible with High-Performance Order Storage (`custom_order_tables`) and modern block-based WooCommerce stores.

= 🌟 Upgrade to Product FAQ Pro =
Want to take your store's pre-sale conversion engine even further? Explore **[Product FAQ Pro at FBS Fusion](https://fbsfusion.com/plugins/#product-faq)**:

* **💬 Customer "Ask a Question" Q&A Portal + AI Auto-Draft**: Let shoppers submit questions directly on product pages with instant AI-drafted answers ready for 1-click approval.
* **🔍 Live Instant FAQ Search Bar**: Real-time search filtering above your accordions so shoppers find answers in milliseconds.
* **⚡ Bulk Category AI Generator & Unlimited Groups**: Generate tailored FAQs across entire product categories simultaneously with unlimited FAQs per product and unlimited Bulk FAQ groups.
* **🛍️ Shop, Category & Tag Archive Page FAQs**: Display SEO-rich FAQ accordions directly on WooCommerce Shop and Category archive pages.
* **🎨 Advanced Card & Toggle Styling**: Custom active accordion backgrounds, rounded card layouts, and custom toggle icons.
* **📊 FAQ Engagement Analytics**: Track total FAQ views, expansion rates, and your store's most-read buyer objections.

= 🛠️ Built by FBS Fusion =
**Product FAQ for WooCommerce** is developed and maintained by **[FBS Fusion](https://fbsfusion.com/)** — a senior WordPress, WooCommerce & Laravel engineering studio specializing in:
* **[95–100 Lighthouse Speed & WooCommerce 502 Rescue](https://fbsfusion.com/speed-security/)**
* **Forensic Malware Eradication & Grade A+ Security Hardening** (also check out our free **[FBS Secure Optimize](https://wordpress.org/plugins/fbs-secure-optimize/)** plugin on WordPress.org!)
* **[Custom WooCommerce Plugin & API Engineering](https://fbsfusion.com/hire-us/)**

= 📖 How to Add FAQs =
1. **Individual Product**: Go to **Products ➔ All Products**, edit any product, open the **FAQ** tab in the **Product Data** box, and click **"✨ Generate with AI"**, choose a template, or write custom rich-HTML FAQs.
2. **Bulk Category / Tag FAQs**: Go to **Product FAQ ➔ Bulk FAQ** to create FAQ groups that automatically display on matching product categories or tags.
3. **Gutenberg Block**: In the WordPress Block Editor or Site Editor, type `/faq` and select **Product FAQ Accordion**.
4. **Universal Shortcode**:
   * `[woo_product_faq]` — Auto-detects the current product on Single Product templates, or displays all Store/Bulk & Product FAQs on a standalone FAQ page.
   * `[woo_product_faq id="123" title="Custom Heading"]` — Renders FAQs for a specific WooCommerce Product ID (`123`).
   * `[woo_product_faq category="hoodies"]` — Renders FAQs filtered to a specific WooCommerce Product Category slug or ID.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/product-faq-for-woocommerce`, or install **Product FAQ for WooCommerce** directly from **Plugins ➔ Add New** in WordPress.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Visit **Product FAQ ➔ Settings** to choose your display position, toggle the `FAQs (N)` tab badge & auto-expand first FAQ, customize colors, or connect an AI provider under **Settings ➔ Connectors**.
4. Edit any WooCommerce product and open the **FAQ** tab in the Product Data panel to start adding FAQs!

== Frequently Asked Questions ==

= Does this plugin support links, bold text, and bullet lists in FAQ answers? =
Yes! Starting in version 1.6.0, both individual Product FAQs and Bulk Category/Tag FAQs support rich HTML formatting (`<a>`, `<strong>`, `<em>`, `<ul>`, `<ol>`, `<li>`) as well as multi-paragraph line breaks, while automatically keeping your Google `FAQPage` JSON-LD schema clean and tag-free.

= Can I use this with Gutenberg Block Themes, Elementor, Divi, or Bricks? =
Yes! You can insert the native **Product FAQ Accordion** Gutenberg block (`/faq`) in the Block Editor / Site Editor, or drop the `[woo_product_faq]` shortcode into Elementor, Divi, Bricks, Oxygen, or any custom template.

= How does the AI FAQ Generator work? =
Product FAQ integrates with WordPress's native AI Client (`Settings ➔ Connectors`) supporting Google Gemini, OpenAI, and Anthropic. When you click **"✨ Generate with AI"** on a product, the plugin reads your product's title, description, price, stock status, weight/dimensions, categories, and attributes (such as Size, Color, and Material) to generate accurate, conversion-ready FAQs. Even if no AI connector is configured yet, built-in smart eCommerce templates work out of the box.

= Can I back up or migrate my FAQ settings and Bulk FAQ groups? =
Yes! Open **Product FAQ ➔ Settings ➔ 💾 Backup & Tools** to export all plugin settings and Bulk FAQ groups to a `.json` file and import them onto any staging or production store in 1 click.

== Screenshots ==

1. FAQ General Settings (Position, Live Tab Count Badge, Auto-Open First FAQ & Google FAQ Schema).
2. AI FAQ Assistant Settings (WordPress Native AI Connectors integration).
3. 1-Click Spec-Aware AI FAQ Generator modal on WooCommerce product edit screen.
4. Pre-built Objection-Buster FAQ Templates dropdown on single product.
5. Google FAQ Schema (JSON-LD) structured data for organic search rich results.
6. FAQ Design & Typography settings.
7. Bulk Category & Tag FAQ Management.
8. Add Rich HTML FAQs for a single product.
9. Responsive FAQ accordion output on single product page.
10. Smart Search Settings (Pro).
11. Live Instant FAQ Search (Pro).
12. Advanced Card & Toggle Styling Settings (Pro).
13. FAQ Engagement Analytics (Pro).

== Changelog ==

= 1.6.0 =
* **New**: Native Gutenberg Block (`Product FAQ Accordion` / `woo-faq/product-faq`) with live editor preview and Inspector controls for Custom Heading, Product ID, and Category Slug.
* **New**: Rich HTML & Multi-Paragraph FAQ Answers — Support clickable links (`<a>`), bold/italic text, bulleted/numbered lists, and automatic paragraph formatting (`wpautop` + `wp_kses_post`) in both Single Product and Bulk FAQs while keeping JSON-LD schema clean.
* **New**: Dynamic Live FAQ Count Badge (`FAQs (5)`) on the WooCommerce Single Product Tab (configurable toggle in General Settings).
* **New**: "Expand First FAQ by Default" conversion toggle in General Settings so shoppers immediately see your top answer on page load.
* **New**: WooCommerce Spec-Aware AI Prompts — AI FAQ Generator now automatically enriches prompts with real WooCommerce product metadata (Price, Stock Status, SKU, Weight & Dimensions, Short Description, Categories/Tags, and Product Attributes).
* **New**: 1-Click JSON Backup Export & Import tool under `Product FAQ ➔ Settings ➔ Backup & Tools` for effortless staging-to-production migration.
* **Improved**: Frontend script caching (replaced dynamic timestamp version with `WOO_FAQ_VERSION`) and scoped multi-accordion click handling so multiple FAQ blocks on the same page operate independently.
* **Improved**: Declared WooCommerce Cart & Checkout Blocks compatibility alongside HPOS (`custom_order_tables`), removed external Google Fonts admin CSS import for faster load & GDPR compliance, and scoped review notices strictly to plugin/product screens.

= 1.5.1 =
* **New**: Universal `[woo_product_faq]` Shortcode — Display product FAQs anywhere inside Elementor, Divi, Bricks, Gutenberg, or custom landing pages using `[woo_product_faq]` or `[woo_product_faq id="123" title="Custom Title"]`, plus a new "Shortcode Only" position option in settings.
* **New**: 3-Tier Category Hierarchy Resolution Engine — Automatically resolves FAQs with strict priority: Individual Product FAQs override Sub-Category FAQs, and Sub-Category FAQs override Main Category FAQs.
* **Updated**: Official video overview and documentation embedded in plugin readme.
* **Improved**: Enhanced WordPress Coding Standards (WPCS) and Plugin Check (PCP) compliance across all admin and frontend modules.

= 1.5.0 =
* **New**: Built-in AI FAQ Assistant integration with WordPress AI Connectors and multi-provider support.
* **New**: Google FAQ Schema (JSON-LD) — Automatically outputs Schema.org `FAQPage` structured data on single product pages for Google rich search snippet eligibility.
* **New**: 1-Click Objection-Buster Templates — Pre-written, eCommerce-tested FAQs for Shipping & Delivery, Returns & Refunds, Warranty, Sizing & Fit, and Care Guides.
* **New**: Product Edit Toolbar — Added top action bar on the WooCommerce Product Data FAQ tab with "✨ Generate with AI" and "💡 Insert FAQ Template..." controls.
* **New**: Dedicated "🤖 AI Assistant" settings tab with real-time AJAX "Test AI Connection" validation.
* **Enhanced**: Redesigned UI styling with isolated CSS scopes to prevent styling conflicts with WooCommerce admin panels and themes.
* **Tested**: Fully tested and compatible with WordPress 7.1 and WooCommerce 11.1.0.

= 1.4.0 =
* Added "Product Tab" position option to render FAQs within custom WooCommerce tabs.

= 1.3.2 =
* Added a polished, dismissible "Review Loop" notification prompt displaying 7 days after installation.
* Hardened CSS styling rules to isolate the settings panel and banners from parent theme style leakage.
* Fully redesigned "Our Plugins" and "Meet the Author" pages with a modern CSS grid portfolio layout.

= 1.3.1 =
* Bumped version for code adjustments.

= 1.3.0 =
* Compatible with WordPress 7.0 and WooCommerce 10.7.0.
* Major overhaul of the Bulk FAQ Management interface with a clean, modern layout.

= 1.2.8 =
* Fix notification issues.

= 1.2.7 =
* Compatible with WooCommerce 10.4.3 version.

= 1.2.6 =
* Compatible with WordPress 6.9 version.

= 1.2.5 =
* Modernized admin dashboard design with improved UI/UX and enhanced security sanitization.

= 1.2.0 =
* Update FAQ group panel design and term validation.

= 1.1.9 =
* Redesign admin panel.

= 1.1.8 =
* Fixed Remove FAQ button issue and single product page display issue.

= 1.1.7 =
* Fixed single page design and tested with WooCommerce 9.8.5.

= 1.1.6 =
* Added bulk questions/answers based on taxonomy terms.

= 1.1.5 =
* Tested with WordPress 6.7.1 and WooCommerce 9.4.3.

= 1.1.4 =
* Added Question & Answer font color and font size options.

= 1.1.2 =
* Added support for WooCommerce High-Performance Order Storage (HPOS).

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.6.0 =
Major Free Upgrade: Adds a native Gutenberg Block (/faq), Rich HTML & clickable links in FAQ answers, live Product Tab count badges (`FAQs (5)`), Auto-Expand First FAQ toggle, WooCommerce Spec-Aware AI generation, and 1-Click JSON Backup Export/Import!
