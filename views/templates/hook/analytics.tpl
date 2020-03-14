<amp-analytics type="gtag" data-credentials="include">
  <script type="application/json">
    {
      "vars": {
        "gtag_id": "{$codeGA}",
        "config": {
          "{$codeGA}": {
            "groups": "default",    
            "site_speed_sample_rate": 100
          }
        }
      },
      "triggers": {
        "button": {
          "selector": "#product-add-to-cart-amp",
          "on": "click",
          "vars": {
            "event_name": "AMP-AddToCart"
          }
        }
      }
    }
  </script>
</amp-analytics>
