// select2_init.js
import $ from 'jquery';
window.$ = $;
window.jQuery = $;
import 'select2';

$(function() {

    // Selecciona los campos utilizando jQuery.
    let $universitySelect = $('.select2-enable[name="generic_opinion_form[university]"]');
    let $degreeSelect = $('.select2-enable[name="generic_opinion_form[degree]"]');
    let $subjectSelect = $('.select2-enable[name="generic_opinion_form[subject]"]');
    let $professorSelect = $('.select2-enable[name="generic_opinion_form[professor]"]');
    let $yearSelect = $('.select2-enable[name="generic_opinion_form[year]"]');

    // Almacena el ID de la universidad seleccionada.
    let selectedUniversityId = '';
    let selectedDegreeId = '';
    let selectedSubjectId = '';
    let selectedProfessorId = '';
    let selectedYear = '';

    // Initialize initial data
    if (typeof initialData === 'undefined') {
        return;
    }
    let initialUniversity = initialData.university;
    let initialDegree = initialData.degree;
    let initialSubject = initialData.subject;
    let initialYear = initialData.year;

    if (initialUniversity.id) {
        let option = new Option(initialUniversity.text, initialUniversity.id, true, true);
        $universitySelect.append(option).trigger('change');
        // manually trigger the `select2:select` event
        $universitySelect.trigger({
            type: 'select2:select',
            params: {
                data: { id: initialUniversity.id, text: initialUniversity.text }
            }
        });
        selectedUniversityId = initialUniversity.id;
    }

    if (initialDegree.id) {
        let option = new Option(initialDegree.text, initialDegree.id, true, true);
        $degreeSelect.append(option).trigger('change');
        $degreeSelect.trigger({
            type: 'select2:select',
            params: {
                data: { id: initialDegree.id, text: initialDegree.text }
            }
        });
        selectedDegreeId = initialDegree.id;
    }

    if (initialSubject.id) {
        let option = new Option(initialSubject.text, initialSubject.id, true, true);
        $subjectSelect.append(option).trigger('change');
        $subjectSelect.trigger({
            type: 'select2:select',
            params: {
                data: { id: initialSubject.id, text: initialSubject.text }
            }
        });
        selectedSubjectId = initialSubject.id;
        selectedYear = initialYear.id;
    }

    // Actualizar cuando se cambia un campo
    $universitySelect.on('select2:select', function (e) {
        let data = e.params.data; // Los datos del elemento seleccionado.
        selectedUniversityId = data.id; // Actualiza el ID de la universidad seleccionada.
    });

    $degreeSelect.on('select2:select', function (e) {
        let data = e.params.data; 
        selectedDegreeId = data.id; 
    });

    $subjectSelect.on('select2:select', function (e) {
        let data = e.params.data; 
        selectedSubjectId = data.id; 
    });

    $('.year-select').on('change', function() {
        selectedYear = $(this).val();
    });

    $professorSelect.on('select2:select', function (e) {
        let data = e.params.data; 
        selectedProfessorId = data.id; 
    });

    // Inicialización de Select2
    $('.select2-enable').select2({
        language: 'es',
        tags: true,
        placeholder: "",
        allowClear: true,
        createTag: function(params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            };
        },
        ajax: {
            url: function(){
                // Aquí seleccionas qué URL usar dependiendo del campo
                let fieldName = $(this).attr('name');
                switch (fieldName) {
                    case 'generic_opinion_form[university]':
                        return "/autocomplete?type=university";
                    case 'generic_opinion_form[degree]':
                        return "/autocomplete?type=degree&university=" + selectedUniversityId;
                    case 'generic_opinion_form[subject]':
                        return "/autocomplete?type=subject&university=" + selectedUniversityId
                        + "&degree=" + selectedDegreeId
                        + "&year=" + selectedYear;
                    case 'generic_opinion_form[professor]':
                        return "/autocomplete?type=professor&university=" + selectedUniversityId
                        + "&degree=" + selectedDegreeId
                        + "&subject=" + selectedSubjectId
                        + "&year=" + selectedYear;
                }
            },
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 1
    });
});
