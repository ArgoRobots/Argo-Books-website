import test from 'node:test';
import assert from 'node:assert/strict';
import { parseTemplateParam } from './url-params.js';

const VALID = ['classic', 'modern', 'minimal', 'bold', 'professional'];

test('parseTemplateParam returns null for empty string', () => {
  assert.equal(parseTemplateParam('', VALID), null);
});

test('parseTemplateParam returns null when param missing entirely', () => {
  assert.equal(parseTemplateParam(null, VALID), null);
  assert.equal(parseTemplateParam(undefined, VALID), null);
});

test('parseTemplateParam returns the value when it is in the allowlist', () => {
  assert.equal(parseTemplateParam('?template=bold', VALID), 'bold');
  assert.equal(parseTemplateParam('?template=professional', VALID), 'professional');
  assert.equal(parseTemplateParam('?foo=1&template=modern&bar=2', VALID), 'modern');
});

test('parseTemplateParam is case insensitive on the value', () => {
  assert.equal(parseTemplateParam('?template=BOLD', VALID), 'bold');
  assert.equal(parseTemplateParam('?template=Professional', VALID), 'professional');
});

test('parseTemplateParam returns null when value is not in the allowlist', () => {
  assert.equal(parseTemplateParam('?template=fancy', VALID), null);
  assert.equal(parseTemplateParam('?template=<script>', VALID), null);
});

test('parseTemplateParam returns null when the param is the empty string', () => {
  assert.equal(parseTemplateParam('?template=', VALID), null);
});
