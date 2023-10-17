function Validator (form) {
    function getParent(element,selector) {
        while (element.parentElement) {
            if(element.parentElement.matches(selector)) {
                return element.parentElement;
            } else {
                element = element.parentElement;
            }
        }
    }
    var formRules = {};

    var validateRules = {
        repuired: function(value) {
                return value.trim() ? undefined : 'Vui lòng nhập thông tin';
        },
        email: function(value) {
                var regex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                return regex.test(value) ? undefined : 'Vui lòng nhập email';
        },
        // confirm: function(inputPass) {
        //     return value === inputPass ? undefined : 'Vui lòng nhập lại chính xác'
        // },
        min: function(min) {
            return function(value) {
                return value.length >= min ? undefined : `Vui lòng nhập đủ ${min} kí tự`;
            }
        }
    };

    var formElement = document.querySelector(form);
    if (formElement) {
        var inputs = document.querySelectorAll('[name][rules]');
        for(var input of inputs) {
            // var inputPass = document.querySelector('#input__password').value;
            var rules = input.getAttribute('rules').split('|');
            for (var rule of rules) {
                var ruleHasValue = rule.includes(':');
                var ruleInfo;
                if (ruleHasValue) {
                    ruleInfo = rule.split(':');
                    rule = ruleInfo[0];
                    ruleFunc = validateRules[ruleInfo[0]](ruleInfo[1]);
                } else {
                    ruleFunc = validateRules[rule];
                }

                if (Array.isArray(formRules[input.name])) {
                    formRules[input.name].push(ruleFunc);
                } else {
                    formRules[input.name] = [ruleFunc];
                }

                input.onblur = handleValidate;
            }

            function handleValidate(event) {
                var rules = formRules[event.target.name];
                var errorMessage;
                rules.find(function (rule) {
                    errorMessage = rule(event.target.value);
                    return errorMessage;
                });
                if (errorMessage) {
                    var formGroup = getParent(event.target,'.form__input');
                    if(formGroup) {
                        formGroup.classList.add('invalid');
                        var formMessage = formGroup.querySelector('.text__error');
                        if(formMessage) {
                            formMessage.innerText = errorMessage;
                        }
                    }

                } else {
                    var formGroup = getParent(event.target,'.form__input');
                    if (formGroup.classList.contains('invalid')) {
                        formGroup.classList.remove('invalid');
                        var formMessage = formGroup.querySelector('.text__error');
                        if(formMessage) {
                            formMessage.innerText = '';
                        }
                    }
                }

                return !errorMessage;
            };
        }
    };

    formElement.onsubmit = function(event) {
        event.preventDefault()
        var inputs = document.querySelectorAll('[name][rules]');
        var isValid = true;
        for(var input of inputs) {
            if  (!handleValidate({target: input})) {
                isValid = false;
            }

            if  (isValid) {
                if  (typeof form.onSubmit === 'function') {
                    var enableInputs = formElement.querySelectorAll('[name]')
                    var formValues = Array.from(enableInputs).reduce(function(values,input) {
                        switch (input.type) {
                            case 'radio':
                                values[input.name] = formElement.querySelector('input[name="' + input.name + '"]:checked').value;
                                break;
                            case 'checkbox':
                                if(!input.matches(':checked')) {
                                    values[input.name] = '';
                                    return values;
                                }
                                if(!Array.isArray(values[input.name])) {
                                    values[input.name] = [];
                                values[input.name].push(input.value);
                                break;
                                }
                            default:
                                values[input.name] = input.value;
                            }
                            return values;
                        }, {});

                        form.onSubmit(formValues);
                } else {
                    
                }
            }
        }
    }
}