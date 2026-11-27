import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

test('font-size controls stay usable when local storage is blocked', () => {
    const script = readFileSync(new URL('../../public/js/ebook-reader.js', import.meta.url), 'utf8');
    const listeners = {};
    const increase = { dataset: { ebookFontChange: '0.1' }, addEventListener: (name, callback) => { listeners.increase = callback; } };
    const decrease = { dataset: { ebookFontChange: '-0.1' }, addEventListener: (name, callback) => { listeners.decrease = callback; } };
    const reset = { dataset: { ebookFontReset: '1' }, addEventListener: (name, callback) => { listeners.reset = callback; } };
    const reader = { style: { fontSize: '' } };
    const output = { textContent: '' };
    const document = {
        addEventListener: (name, callback) => { listeners.ready = callback; },
        getElementById: () => output,
        querySelector: (selector) => selector === '[data-ebook-reader]' ? reader : output,
        querySelectorAll: (selector) => selector.includes('font-change') ? [increase, decrease] : [reset],
    };
    const blockedStorage = {
        getItem() { throw new Error('Storage is blocked'); },
        setItem() { throw new Error('Storage is blocked'); },
    };

    vm.runInNewContext(script, { document, localStorage: blockedStorage });
    listeners.ready();
    assert.doesNotThrow(() => listeners.increase());
    assert.equal(reader.style.fontSize, '1.265rem');
    assert.equal(output.textContent, '110%');
    assert.doesNotThrow(() => listeners.reset());
    assert.equal(reader.style.fontSize, '1.15rem');
});

test('font-size preference loads from storage and stays within its supported range', () => {
    const script = readFileSync(new URL('../../public/js/ebook-reader.js', import.meta.url), 'utf8');
    const listeners = {};
    let savedScale = '1.3';
    const increase = { dataset: { ebookFontChange: '0.1' }, addEventListener: (name, callback) => { listeners.increase = callback; } };
    const reader = { style: { fontSize: '' } };
    const output = { textContent: '' };
    const document = {
        addEventListener: (name, callback) => { listeners.ready = callback; },
        getElementById: () => output,
        querySelector: () => reader,
        querySelectorAll: (selector) => selector.includes('font-change') ? [increase] : [],
    };
    const localStorage = {
        getItem: () => savedScale,
        setItem: (key, value) => { savedScale = value; },
    };

    vm.runInNewContext(script, { document, localStorage });
    listeners.ready();
    assert.equal(output.textContent, '130%');
    listeners.increase();
    assert.equal(output.textContent, '140%');
    assert.equal(savedScale, '1.4');
});
