#!/usr/bin/env node
/*
 * Produce artifact.html from index.html.
 *
 * The two differ only in their outer wrapper. A standalone page needs its own
 * doctype -- without one a browser opens it in quirks mode -- while the
 * Artifact runtime supplies the doctype, head and body itself and expects the
 * file to be the contents alone. Everything else, the whole program included,
 * is shared, so it is generated rather than kept as a second copy.
 */
const fs = require('fs');
const path = require('path');

const src = fs.readFileSync(path.join(__dirname, 'index.html'), 'utf8');
let out = src;

const drop = [
  /^<!doctype html>\n/i,
  /^<html[^>]*>\n/i,
  /^<head>\n/im,
  /^<\/head>\n/im,
  /^<body>\n/im,
  /^<\/body>\n/im,
  /^<\/html>\n?/im,
  /^<meta charset="utf-8">\n/im,
  /^<meta name="viewport"[^>]*>\n/im,
  /^<meta name="robots"[^>]*>\n/im,
];
for (const re of drop) {
  if (!re.test(out)) { console.error('build: nothing matched ' + re); process.exit(1); }
  out = out.replace(re, '');
}

if (!/^<title>/.test(out.trim())) {
  console.error('build: the artifact file must begin with its <title>');
  process.exit(1);
}
/* Anchored so that <header> is not mistaken for a stray <head>. */
for (const re of [/<!doctype/i, /<html[\s>]/i, /<head[\s>]/i, /<body[\s>]/i, /<\/(html|head|body)>/i]) {
  const hit = out.match(re);
  if (hit) {
    console.error('build: ' + hit[0] + ' still present');
    process.exit(1);
  }
}

fs.writeFileSync(path.join(__dirname, 'artifact.html'), out.trim() + '\n');
console.log('artifact.html  ' + out.length + ' bytes');
