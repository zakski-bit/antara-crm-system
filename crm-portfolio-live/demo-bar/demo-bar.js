(function () {
  // Determine if we are inside admin/ or root
  const pathname = window.location.pathname.toLowerCase();
  const isAdmin = pathname.includes('/admin/') || (pathname.endsWith('customer.html') && !pathname.includes('/root/'));
  
  const rootPath = isAdmin ? '../' : './';
  const adminPath = isAdmin ? './' : './admin/';

  // Current page detection
  let current = 'home';
  if (pathname.endsWith('login.html')) {
    current = 'login';
  } else if (pathname.endsWith('customer.html')) {
    current = 'customer';
  } else if (pathname.includes('/admin/') || pathname.endsWith('dashboard-admin.html')) {
    current = 'admin';
  }

  const bar = document.createElement('div');
  bar.id = 'portfolio-demo-bar';
  bar.innerHTML = `
    <div class="pdb-badge">
      <span class="pdb-badge-dot"></span>
      Demo Portofolio
    </div>
    <div class="pdb-divider"></div>
    <ul class="pdb-links">
      <li><a href="${rootPath}index.html" class="${current === 'home' ? 'active' : ''}">🌐 Beranda</a></li>
      <li><a href="${rootPath}login.html" class="${current === 'login' ? 'active' : ''}">🔐 Login Mockup</a></li>
      <li><a href="${adminPath}index.html" class="${current === 'admin' ? 'active' : ''}">📊 Admin CRM</a></li>
      <li><a href="${adminPath}customer.html" class="${current === 'customer' ? 'active' : ''}">👤 Portal Klien</a></li>
    </ul>
    <div class="pdb-divider"></div>
    <button class="pdb-toggle" title="Sembunyikan / Tampilkan bar demo" onclick="document.getElementById('portfolio-demo-bar').classList.toggle('collapsed')">✕</button>
  `;

  document.body.appendChild(bar);
})();
