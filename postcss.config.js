module.exports = (ctx) => ({
  plugins: {
    "postcss-import": {},
    "postcss-preset-env": {
      stage: 1,
      preserve: true,
      features: {
        'custom-media-queries': {
          importFrom: 'theme/css/media.css'
        }
      }
    },
    "autoprefixer": {},
    "cssnano":
      ctx.env === "production"
        ? { preset: "default" }
        : false
  }
});
