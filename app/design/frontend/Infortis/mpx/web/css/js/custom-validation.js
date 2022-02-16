
Validation.add('validate-address-custom','Please use only letters (a-z or A-Z), numbers (0-9), the following characters(-_#) or space.',function(v){
	return Validation.get('IsEmpty').test(v) || /^[a-zA-Z0-9-àâäôéèëêïîçùûüÿæœÀÂÄÔÉÈËÊÏÎŸÇÙÛÜÆŒößÖZąćęłńóśźżĄĆĘŁŃÓŚŹŻìíòúÌÍÒÚáñÁÑ_#'.\s]+$/.test(v);
});

Validation.add('validate-has-digit','Please use at least one digit (0-9).',function(v){
    return Validation.get('IsEmpty').test(v) || /\d/.test(v);
});

// we need to add multiple similar functions for these because we cannot easily have error messages having the actual length limit in it.
Validation.add('validate-maximum-length-40-custom', 'The maximum length for this field is 40 characters.', function (v) {
    return Validation.get('IsEmpty').test(v) ||  v.length <= 40;
});
Validation.add('validate-maximum-length-10-custom', 'The maximum length for this field is 10 characters.', function (v) {
    return Validation.get('IsEmpty').test(v) ||  v.length <= 10;
});
Validation.add('validate-maximum-length-15-custom', 'The maximum length for this field is 15 characters.', function (v) {
    return Validation.get('IsEmpty').test(v) ||  v.length <= 15;
});
Validation.add('validate-minimum-length-10-custom', 'The minimum length for this field is 10 characters.', function (v) {
    return Validation.get('IsEmpty').test(v) ||  v.length >= 10;
});
