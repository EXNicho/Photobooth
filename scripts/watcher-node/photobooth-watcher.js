#!/usr/bin/env node
/*
  Photobooth Watcher (Node.js)
  - Mengamati folder lokal dan mengirim foto baru ke API Laravel
  - Idempoten via checksum sha256
  Config melalui env:
    WATCH_DIR=C:\\Photobooth\\out
    API_URL=http://photobooth.test/api/photos
    API_TOKEN=xxxxx
    EVENT_ID=event-01 (opsional)
*/

import fs from 'fs';
import path from 'path';
import crypto from 'crypto';
import FormData from 'form-data';
import axios from 'axios';
import chokidar from 'chokidar';
import os from 'os';

const WATCH_DIR = process.env.WATCH_DIR || 'C:/Photobooth/out';
const API_URL = process.env.API_URL || 'http://photobooth.test/api/photos';
const API_TOKEN = process.env.API_TOKEN || '';
const EVENT_ID = process.env.EVENT_ID || '';

if (!API_TOKEN) {
  console.error('API_TOKEN kosong. Set env API_TOKEN.');
  process.exit(1);
}

function sleep(ms){ return new Promise(r=>setTimeout(r,ms)); }

async function sha256File(filePath) {
  return new Promise((resolve, reject) => {
    const hash = crypto.createHash('sha256');
    const stream = fs.createReadStream(filePath);
    stream.on('error', reject);
    stream.on('data', (d) => hash.update(d));
    stream.on('end', () => resolve(hash.digest('hex')));
  });
}

async function postFile(filePath){
  const stat = fs.statSync(filePath);
  const checksum = await sha256File(filePath);
  const form = new FormData();
  form.append('file', fs.createReadStream(filePath), path.basename(filePath));
  form.append('original_name', path.basename(filePath));
  form.append('size', stat.size.toString());
  form.append('checksum', checksum);
  if (EVENT_ID) form.append('event_id', EVENT_ID);

  const headers = { ...form.getHeaders(), Authorization: `Bearer ${API_TOKEN}` };
  for (let attempt=1; attempt<=5; attempt++) {
    try {
      const res = await axios.post(API_URL, form, { headers, timeout: 30000 });
      console.log(`[OK] ${path.basename(filePath)} -> ${res.status} ${res.data?.id || ''}`);
      return true;
    } catch (e) {
      const code = e.response?.status || e.code || 'ERR';
      console.warn(`[Retry ${attempt}] ${path.basename(filePath)} -> ${code}`);
      await sleep(1000 * attempt);
    }
  }
  console.error(`[FAIL] ${path.basename(filePath)}`);
  return false;
}

function isImage(file){
  return /\.(jpe?g|png|webp|heic)$/i.test(file);
}

console.log(`Watching: ${WATCH_DIR}`);
chokidar.watch(WATCH_DIR, { ignoreInitial: true, depth: 0 })
  .on('add', async (file) => {
    if (!isImage(file)) return;
    // Tunggu sampai file stabil (selesai ditulis)
    await sleep(800);
    try { await postFile(file); } catch (e) { console.error(e); }
  });

