/**
 * Created by v_lhuilan on 2018/5/10.
 * 有重复冗余代码待精简（optgroup）
 */


;
(function ($, window) {


    $.fn.userselect = function (options) {

        var settings = $.extend({
            'class': '',
            'placeholder': 'انتخاب کاربر',
            'dropClass': '',
            'dropFooter': true,
            'isOpen': false,
            'maxTag': 10,
            'tagParent': 'qmain2',
            'tagMessage': 'Up To 4 Tags',
            'tagMessageHide': '3000',
            'tagMessageStyle': ''
        }, options);

        return this.each(function () {
            var self = this;
            if (this.userselect || !$(this).is('select')) {
                return;
            }
            this.userselect = {

                E: $(self),
                init: function () {
                    var _this = this;
                    _this.createElems();
                    // _this.showDrop();
                    // $("body").unbind('click');
                    $("body").bind('click', _this.hideDrop);

                    $("body").undelegate('.quserselect', 'click');
                    $("body").delegate('.quserselect', 'click', function (e) {
                        e.stopPropagation();
                    });

                    $("body").undelegate('.quserselect__choices', 'click');
                    $("body").delegate('.quserselect__choices', 'click', _this.showDrop);

                    $("body").undelegate('.quserselect__closelink', 'click');
                    $("body").delegate('.quserselect__closelink', 'click', _this.hideDrop);

                    $("body").undelegate('.quserselect__option', 'click');
                    $("body").delegate('.quserselect__option', 'click', _this.showOpt);

                    $("body").undelegate('.quserselect__del', 'click');
                    $("body").delegate('.quserselect__del', 'click', _this.removeOpt);


                    return _this;
                },
                createElems: function () {
                    var _this = this,
                        _opt = '',
                        _container = '',
                        _hd = '',
                        _drop = '';
                    if (_this.E.attr('disabled')) {
                        settings.class += ' isdisabled';
                    }

                    _hd = _this.createHd(_this.E.children());
                    _drop = '<div class="quserselect__drop' + (settings.dropClass === '' ? '' : ' ' + settings.dropClass) + '"><ul class="quserselect__results">' + _this.createOptions(_this.E.children()) + '</ul>' + _this.createDropFooter() + '</div>';

                    _container = '<div class="quserselect__container">' + _hd + _drop + '</div>';
                    $('.quserselect').append(_container);
                },
                createHd: function (els) {
                    var _this = this,
                        id = 0,
                        hd = '',
                        field = '',
                        selectOpt = [],
                        selectsubOpt = [],
                        _class = '';

                    els.each(function (index, el) {
                        id += 1;
                        _class = $(el).attr('class') ? ' ' + $(el).attr('class') : '';
                        if ($(el).is('optgroup')) {
                            $(el).children().each(function (index, subel) {
                                id += 1;
                                if ($(subel).attr('selected')) {
                                    selectOpt.push('<li class="quserselect__choice ' + _class + '" data-cid="' + id + '"><span>' + $(subel).text() + '</span><a class="quserselect__del"></a></li>');
                                }
                            })
                        } else {
                            if ($(el).attr('selected')) {
                                selectOpt.push('<li class="quserselect__choice ' + _class + '" data-cid="' + id + '"><span>' + $(el).text() + '</span><a class="quserselect__del"></a></li>');
                            }
                        }
                    })
                    // console.log(_this.E.find('[selected]').length)
                    if (_this.E.find('[selected]').length > 0) {
                        field = '<li class="quserselect__field"><span class="quserselect__btn"></span></li>';
                    } else {
                        field = '<li class="quserselect__field"><span class="quserselect__placeholder">' + settings.placeholder + '</span><span class="quserselect__btn"></span></li>';
                    }

                    hd = '<ul class="quserselect__choices">' + selectOpt.join('') + field + '</ul>';
                    return hd;
                },
                createOptions: function (els) {
                    var _this = this,
                        id = 0,
                        _opt = [],
                        _drop = '',
                        _val = '',
                        _isdisabled = '',
                        _isselected = '',
                        _class = '',
                        _title = '';
                    els.each(function (index, el) {
                        id += 1;
                        // console.log(index, id)
                        _val = $(el).attr('value') ? 'data-value=' + $(el).val() : '';
                        _title = $(el).attr('title') ? 'title=' + $(el).attr('title') : '';
                        _class = $(el).attr('class') ? ' ' + $(el).attr('class') : '';
                        _isdisabled = $(el).attr('disabled') ? ' isdisabled' : '';
                        _isselected = $(el).attr('selected') ? ' isselected' : '';

                        if ($(el).is('optgroup')) {
                            var _subopt = [];
                            // console.log(id)
                            $(el).children().each(function (index, subel) {
                                id += 1;
                                // console.log(index, id)
                                var _subval = '',
                                    _subtitle = '',
                                    _subclass = '',
                                    _subdisabled = '',
                                    _subisselect = '';
                                _subval = $(subel).attr('value') ? 'data-value=' + $(subel).val() : '';
                                _subtitle = $(subel).attr('title') ? 'title=' + $(subel).attr('title') : '';
                                _subclass = $(subel).attr('class') ? ' ' + $(subel).attr('class') : '';
                                _subdisabled = $(subel).attr('disabled') ? ' isdisabled' : '';
                                _subisselect = $(subel).attr('selected') ? ' isselected' : '';
                                _subopt.push('<li class="quserselect__option' + _subclass + _subdisabled + _subisselect + '" data-rid="' + id + '" ' + _subval + _subtitle + '>' + $(subel).html() + '</li>');
                            })
                            _opt.push('<li class="quserselect__group' + _class + _isdisabled + '"><div class="quserselect__group_label">' + $(el).attr('label') + '</div><ul>' + _subopt.join('') + '</ul></li>')
                        } else {
                            _opt.push('<li class="quserselect__option' + _class + _isdisabled + _isselected + '" data-rid="' + id + '" ' + _val + _title + '>' + $(el).html() + '</li>');
                        }
                    })
                    // _drop = '<div class="quserselect__drop' + (settings.dropClass === '' ? '' : ' ' + settings.dropClass) + '"><ul class="quserselect__results">' + _opt.join('') + '</ul></div>';
                    return _opt.join('');
                },
                createDropFooter: function () {
                    var _this = this,
                        dropFooter = '';
                    if (settings.dropFooter) {
                        dropFooter = '<div class="quserselect__drop__ft"><a href="javascript:void(0);" class="quserselect__closelink">بستن<span class="quserselect__triangle"></span></a></div>';
                    }
                    return dropFooter;
                },
                showDrop: function (e) {
                    var _this = $(this);
                    var parent = _this.closest('.quserselect');
                    var select = parent.find('select');
                    if (select.attr('disabled') || parent.hasClass('isdisabled')) {
                        return;
                    }
                    parent.addClass('isopen');
                    e.stopPropagation();
                },
                hideDrop: function () {
                    var _this = $(this);
                    var parent = _this.closest('.quserselect');
                    if (parent.length > 0) {
                        parent.removeClass('isopen');

                    } else {
                        $('.quserselect').removeClass('isopen');
                    }

                },
                showOpt: function (e) {
                    var _this = $(this),
                        _class = '';
                    var parent = _this.closest('.quserselect');
                    var ul = parent.find('.quserselect__choices');
                    if (_this.hasClass('isselected') || _this.hasClass('isdisabled')) {
                        return;
                    }
                    ul.find('.quserselect__field .quserselect__placeholder').remove();

                    // console.log(ul.find('.quserselect__choice').length)
                    if (ul.find('.quserselect__choice').length < settings.maxTag) {
                        _class = _this.attr('class').slice(19);
                        ul.append('<li class="quserselect__choice ' + _class + '" data-cid="' + _this.attr("data-rid") + '"><span>' + _this.html() + '</span><a class="quserselect__del"></a></li>');
                        _this.addClass('isselected');
                        if (parent.find('.quserselect__select').val() == _this.attr('data-value')) {

                        }
                        parent.find('.quserselect__select').find('option[value="' + _this.attr('data-value') + '"]').attr("selected", true);
                        parent.find('.quserselect__select').trigger('change');
                        // console.log(_this.attr('data-value'), parent.find('.quserselect__select').find('option[value="1"]').text());
                    } else {
                        $('.' + settings.tagParent).append('<div class="quserselect__toast" style="' + settings.tagMessageStyle + '">' + settings.tagMessage + '</div>')
                        $('.quserselect__toast').fadeIn();
                        if (settings.tagMessageHide !== 'false') {
                            window.setTimeout(function () {
                                $('.quserselect__toast').fadeOut(function () {
                                    $(this).remove();
                                });
                            }, settings.tagMessageHide);

                        }
                    }
                    e.stopPropagation();
                },
                removeOpt: function (e) {
                    var _this = $(this);
                    var parent = _this.closest('.quserselect__choice');
                    var ul = parent.closest('.quserselect__choices');
                    var opt = ul.siblings('.quserselect__drop').find('[data-rid="' + parent.attr('data-cid') + '"]');
                    parent.remove();
                    opt.removeClass('isselected');

                    ul.closest('.quserselect').find('.quserselect__select').find('option[value="' + opt.attr('data-value') + '"]').removeAttr("selected");
                    // parent.find('.quserselect__select').find('option[value="' + _this.attr('data-value') + '"]').attr("selected", true);
                    ul.closest('.quserselect').find('.quserselect__select').trigger('change');
                    // console.log(ul.closest('.quserselect').find('.quserselect__select').val(),opt.attr('data-value'));
                    if (!ul.find('.quserselect__choice').length > 0) {
                        ul.find('.quserselect__field').prepend('<span class="quserselect__placeholder">' + settings.placeholder + '</span>');
                    }
                    e.stopPropagation();
                }
            };

            self.userselect.init();
        })
    };
}(jQuery, window));
