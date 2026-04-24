const fs = require('fs');
const path = require('path');

const projectRoot = path.resolve(__dirname, '..', '..');
const mobileRoot = path.resolve(__dirname, '..');
const webDir = path.join(mobileRoot, 'www');

const filesToCopy = [
  ['app.html', 'index.html'],
  ['login.html', 'login.html'],
  ['admin_dashboard.html', 'admin_dashboard.html'],
  ['user_dashboard.html', 'user_dashboard.html'],
  ['api_auth.php', 'api_auth.php'],
  ['server.php', 'server.php'],
  ['server_network.php', 'server_network.php'],
  ['server_mobile.php', 'server_mobile.php']
];

fs.mkdirSync(webDir, { recursive: true });

for (const [sourceName, targetName] of filesToCopy) {
  const source = path.join(projectRoot, sourceName);
  const target = path.join(webDir, targetName);

  if (!fs.existsSync(source)) {
    console.warn(`[sync-web] Missing file: ${sourceName}`);
    continue;
  }

  fs.copyFileSync(source, target);
  console.log(`[sync-web] Copied ${sourceName} -> ${targetName}`);
}

console.log('[sync-web] Done.');
