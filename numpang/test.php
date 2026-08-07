<!DOCTYPE html>
<html>
<head>
    <title>Fingerprint Test</title>

    <script>
      async function initFingerprintJS() {
        try {
          // Load the FingerprintJS agent
          const fp = await FingerprintJS.load();
    
          // Get the visitor identifier
          const result = await fp.get();
    
          // This is the visitor's unique ID
          const visitorId = result.visitorId;
          console.log('FingerprintJS Visitor ID:', visitorId);

        } catch (error) {
          console.error('Error getting fingerprint:', error);
        }
      }
    </script>

    <script
      src="https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@4/dist/fp.min.js"
      onload="initFingerprintJS()">
    </script>

</head>
<body>
    <h1>Check the console for the Visitor ID.</h1>
</body>
</html>