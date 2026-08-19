import { stat } from 'node:fs/promises';
import { captureAll } from './capture.mjs';

const smokePage = { label: 'Home (smoke test)', url: 'https://looptrails.com/' };
const manifest = await captureAll([smokePage]);

if (manifest.length !== 5) {
  throw new Error(`Expected 5 viewport captures, got ${manifest.length}`);
}

for (const entry of manifest) {
  const filePath = entry.file.replace('docs/', '../../docs/');
  const s = await stat(filePath);
  if (s.size === 0) {
    throw new Error(`${entry.file} is empty (0 bytes)`);
  }
  if (entry.status !== 200) {
    throw new Error(`${entry.file} got HTTP status ${entry.status}, expected 200`);
  }
}

console.log('Smoke test passed: 5 non-empty screenshots at HTTP 200.');
