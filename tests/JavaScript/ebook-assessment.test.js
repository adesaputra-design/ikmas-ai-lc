import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const source = readFileSync(new URL('../../public/js/ebook-assessment.js', import.meta.url), 'utf8');
const window = {};
vm.runInNewContext(source, { window, document: { addEventListener() {} } });

const definition = {
    scale: { min: 1, max: 5 },
    principles: [
        { name: 'Refleksi', questions: [{ id: 'r1' }, { id: 'r2' }] },
        { name: 'Aksi', questions: [{ id: 'a1' }] },
    ],
    ranges: [{ min: 3, max: 15, label: 'Terus bertumbuh', description: 'Lanjutkan.', recommendation_chapters: ['refleksi'] }],
};

test('assessment rejects unanswered or out-of-scale answers', () => {
    assert.match(window.calculateEbookAssessment(definition, { r1: '3' }).error, /Jawab semua/);
    assert.match(window.calculateEbookAssessment(definition, { r1: '3', r2: '3', a1: '8' }).error, /di luar skala/);
});

test('assessment calculates principle averages and selects the matching recommendation', () => {
    const result = window.calculateEbookAssessment(definition, { r1: '5', r2: '3', a1: '2' });

    assert.equal(result.total, 10);
    assert.deepEqual(JSON.parse(JSON.stringify(result.principleScores)), [
        { name: 'Refleksi', score: 4 },
        { name: 'Aksi', score: 2 },
    ]);
    assert.equal(result.range.label, 'Terus bertumbuh');
    assert.deepEqual(JSON.parse(JSON.stringify(result.range.recommendation_chapters)), ['refleksi']);
});
