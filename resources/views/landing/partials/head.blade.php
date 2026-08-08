<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title ?? 'Absensija' }}</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
<style>
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    display: inline-block;
    line-height: 1;
  }

  .glass-card {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(226, 232, 240, 0.8);
  }

  body {
    font-family: 'Inter', sans-serif;
    scroll-behavior: smooth;
  }
</style>
<script id="tailwind-config">
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        "colors": {
          "on-primary-fixed-variant": "#003ea8",
          "primary-fixed": "#dbe1ff",
          "on-primary-fixed": "#00174b",
          "on-surface-variant": "#434655",
          "surface-container-lowest": "#ffffff",
          "secondary-fixed-dim": "#6bd8cb",
          "on-error-container": "#93000a",
          "secondary-container": "#86f2e4",
          "tertiary-fixed-dim": "#ffb596",
          "on-tertiary-fixed-variant": "#7d2d00",
          "tertiary": "#943700",
          "on-background": "#0b1c30",
          "secondary-fixed": "#89f5e7",
          "primary-fixed-dim": "#b4c5ff",
          "surface-variant": "#d3e4fe",
          "primary": "#004ac6",
          "surface-container-low": "#eff4ff",
          "on-secondary-container": "#006f66",
          "on-tertiary-container": "#ffede6",
          "surface-container": "#e5eeff",
          "outline-variant": "#c3c6d7",
          "background": "#f8f9ff",
          "error-container": "#ffdad6",
          "on-secondary-fixed-variant": "#005049",
          "inverse-surface": "#213145",
          "on-error": "#ffffff",
          "primary-container": "#2563eb",
          "error": "#ba1a1a",
          "on-tertiary-fixed": "#360f00",
          "surface-tint": "#0053db",
          "outline": "#737686",
          "on-primary": "#ffffff",
          "tertiary-container": "#bc4800",
          "secondary": "#006a61",
          "surface": "#f8f9ff",
          "surface-bright": "#f8f9ff",
          "on-surface": "#0b1c30",
          "on-secondary-fixed": "#00201d",
          "inverse-primary": "#b4c5ff",
          "on-primary-container": "#eeefff",
          "surface-container-high": "#dce9ff",
          "inverse-on-surface": "#eaf1ff",
          "surface-dim": "#cbdbf5",
          "tertiary-fixed": "#ffdbcd",
          "surface-container-highest": "#d3e4fe",
          "on-tertiary": "#ffffff",
          "on-secondary": "#ffffff"
        },
        "borderRadius": {
          "DEFAULT": "0.25rem",
          "lg": "0.5rem",
          "xl": "0.75rem",
          "full": "9999px"
        },
        "spacing": {
          "lg": "40px",
          "md": "24px",
          "xs": "4px",
          "margin-mobile": "16px",
          "base": "8px",
          "margin-desktop": "48px",
          "xl": "64px",
          "gutter": "24px",
          "sm": "12px"
        },
        "fontFamily": {
          "headline-lg": ["Inter"],
          "label-md": ["Inter"],
          "display-lg": ["Inter"],
          "label-sm": ["Inter"],
          "body-md": ["Inter"],
          "headline-md": ["Inter"],
          "body-lg": ["Inter"],
          "body-sm": ["Inter"]
        },
        "fontSize": {
          "headline-lg-mobile": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
          "label-md": ["14px", { "lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "500" }],
          "display-lg": ["48px", { "lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
          "label-sm": ["12px", { "lineHeight": "16px", "fontWeight": "600" }],
          "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
          "headline-md": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
          "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
          "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
          "body-sm": ["14px", { "lineHeight": "20px", "fontWeight": "400" }]
        }
      },
    },
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

