<!DOCTYPE html>
<html lang="en">
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-H3MS6X8GNJ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-H3MS6X8GNJ');
</script>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tenant Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-body">
            <h1 class="h4 mb-3">Tenant Dashboard</h1>
            <p class="mb-1"><strong>Tenant ID:</strong> {{ $tenantId }}</p>
            <p class="mb-1"><strong>Current DB:</strong> {{ $dbName }}</p>
            <hr />

            @auth
              @if (isset($billingUrl) && $billingUrl && (auth()->user()->role ?? null) === 'superadmin')
                <a href="{{ $billingUrl }}" class="btn btn-outline-primary">Manage Billing</a>
              @endif
            @endauth

            <p class="text-muted mt-3 mb-0">If you can see a tenant-specific database name here, DB switching works.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
