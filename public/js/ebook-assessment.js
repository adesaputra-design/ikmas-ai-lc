function calculateEbookAssessment(definition, answers) {
    const scaleMin = Number(definition.scale.min);
    const scaleMax = Number(definition.scale.max);
    const questions = definition.principles.flatMap((principle) => principle.questions.map((question) => ({
        principle,
        question,
    })));

    if (Object.keys(answers).length !== questions.length) {
        return { error: 'Jawab semua pertanyaan untuk melihat hasil.' };
    }

    let total = 0;
    const principleScores = definition.principles.map((principle) => {
        const values = principle.questions.map(({ id }) => Number(answers[id]));
        if (values.some((value) => !Number.isInteger(value) || value < scaleMin || value > scaleMax)) {
            return null;
        }

        const score = values.reduce((sum, value) => sum + value, 0);
        total += score;

        return {
            name: principle.name,
            score: Math.round((score / values.length) * 100) / 100,
        };
    });

    if (principleScores.some((score) => score === null)) {
        return { error: 'Ada jawaban di luar skala. Periksa kembali pilihanmu.' };
    }

    const range = definition.ranges.find((candidate) => total >= Number(candidate.min) && total <= Number(candidate.max));
    if (!range) {
        return { error: 'Rentang hasil asesmen belum mencakup skor ini.' };
    }

    return { total, range, principleScores };
}

if (typeof window !== 'undefined') {
    window.calculateEbookAssessment = calculateEbookAssessment;

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('ebook-assessment-form');
        if (!form) {
            return;
        }

        const definition = JSON.parse(document.getElementById('ebook-assessment-definition').textContent);
        const error = document.getElementById('ebook-assessment-error');
        const result = document.getElementById('ebook-assessment-result');
        const rangeOutput = document.getElementById('ebook-assessment-range');
        const principlesOutput = document.getElementById('ebook-assessment-principles');
        const recommendationsOutput = document.getElementById('ebook-assessment-recommendations');
        const chapterUrlTemplate = document.getElementById('ebook-assessment-chapter-url').dataset.template;
        const saveForm = document.getElementById('ebook-assessment-save-form');
        const saveAnswers = document.getElementById('ebook-assessment-save-answers');

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const answers = {};
            definition.principles.forEach((principle) => principle.questions.forEach((question) => {
                const fields = form.elements.namedItem(question.id);
                if (fields?.value) {
                    answers[question.id] = fields.value;
                }
            }));

            const calculated = calculateEbookAssessment(definition, answers);
            if (calculated.error) {
                error.textContent = calculated.error;
                result.hidden = true;
                return;
            }

            error.textContent = '';
            rangeOutput.textContent = `${calculated.range.label} · ${calculated.range.description} (skor total ${calculated.total})`;
            principlesOutput.replaceChildren();
            const scoreList = document.createElement('ul');
            calculated.principleScores.forEach((principle) => {
                const item = document.createElement('li');
                item.textContent = `${principle.name}: ${principle.score}`;
                scoreList.append(item);
            });
            principlesOutput.append(scoreList);

            recommendationsOutput.replaceChildren();
            const heading = document.createElement('h3');
            heading.textContent = 'Bab yang disarankan';
            recommendationsOutput.append(heading);
            const chapters = document.createElement('ul');
            calculated.range.recommendation_chapters.forEach((slug) => {
                const item = document.createElement('li');
                const link = document.createElement('a');
                link.href = chapterUrlTemplate.replace('__chapter__', encodeURIComponent(slug));
                link.textContent = `Baca ulang bab ${slug}`;
                item.append(link);
                chapters.append(item);
            });
            recommendationsOutput.append(chapters);
            if (saveForm && saveAnswers) {
                saveAnswers.replaceChildren();
                Object.entries(answers).forEach(([questionId, value]) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `answers[${questionId}]`;
                    input.value = value;
                    saveAnswers.append(input);
                });
                saveForm.hidden = false;
            }
            result.hidden = false;
            result.scrollIntoView?.({ behavior: 'smooth', block: 'start' });
        });
    });
}
