<script>
  // For rounding tkile coords
  function round_down(n) {
    if (n > 0) {
      return Math.ceil(n / tile_size) * tile_size;
    } else if (n < 0) {
      return Math.ceil(n / tile_size) * tile_size;
    } else {
      return 0;
    }
  }

  // Uppercase words
  function ucwords(str) {
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function($1) {
      return $1.toUpperCase();
    });
  }

  // For number formatting
  function number_format(nStr) {
    if (!nStr) {
      return 0;
    }
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
      x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
  }
</script>