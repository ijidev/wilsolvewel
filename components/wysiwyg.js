(function () {
    'use strict';

    function initWysiwyg(textarea) {
        if (textarea.dataset.wysiwygInitialized) return;

        var uid = 'wysiwyg-' + Math.random().toString(36).slice(2, 8);
        textarea.dataset.wysiwygInitialized = uid;

        var wrapper = document.createElement('div');
        wrapper.className = 'wysiwyg-wrapper';
        wrapper.style.cssText = 'border:1px solid #E2E8F0;border-radius:16px;overflow:hidden;background:#F8FAFC;transition:border-color .2s';

        // Toolbar
        var toolbar = document.createElement('div');
        toolbar.className = 'wysiwyg-toolbar';
        toolbar.style.cssText = 'display:flex;flex-wrap:wrap;gap:2px;padding:8px;border-bottom:1px solid #E2E8F0;background:#fff';

        var buttons = [
            { cmd: 'bold',         icon: 'format_bold',          title: 'Bold' },
            { cmd: 'italic',       icon: 'format_italic',        title: 'Italic' },
            { cmd: 'underline',    icon: 'format_underlined',    title: 'Underline' },
            { type: 'sep' },
            { cmd: 'insertUnorderedList', icon: 'format_list_bulleted', title: 'Bullet List' },
            { cmd: 'insertOrderedList',   icon: 'format_list_numbered', title: 'Numbered List' },
            { type: 'sep' },
            { cmd: 'link',         icon: 'link',                 title: 'Insert Link' },
            { type: 'sep' },
            { cmd: 'source',       icon: 'code',                 title: 'Source' }
        ];

        buttons.forEach(function (btn) {
            if (btn.type === 'sep') {
                var sep = document.createElement('div');
                sep.style.cssText = 'width:1px;height:24px;background:#E2E8F0;margin:0 4px';
                toolbar.appendChild(sep);
                return;
            }

            var el = document.createElement('button');
            el.type = 'button';
            el.title = btn.title;
            el.innerHTML = '<span class="material-symbols-outlined" style="font-size:18px;line-height:1">' + btn.icon + '</span>';
            el.style.cssText = 'width:32px;height:32px;display:flex;align-items:center;justify-content:center;border:none;border-radius:8px;background:transparent;color:#64748B;cursor:pointer;transition:all .15s';
            el.dataset.cmd = btn.cmd;

            el.addEventListener('mouseenter', function () { this.style.background = '#F1F5F9'; this.style.color = '#0F172A'; });
            el.addEventListener('mouseleave', function () { if (!this.classList.contains('active')) { this.style.background = 'transparent'; this.style.color = '#64748B'; } });

            if (btn.cmd === 'source') {
                el.addEventListener('click', function () {
                    toggleSource(editor, textarea, el);
                });
            } else if (btn.cmd === 'link') {
                el.addEventListener('click', function () {
                    var url = prompt('Enter URL:');
                    if (url) {
                        var clean = url.trim();
                        if (clean && !/^https?:\/\//i.test(clean)) clean = 'https://' + clean;
                        document.execCommand('createLink', false, clean);
                        editor.focus();
                        syncContent();
                    }
                });
            } else {
                el.addEventListener('click', function () {
                    document.execCommand(this.dataset.cmd, false, null);
                    editor.focus();
                    syncContent();
                });
            }

            toolbar.appendChild(el);
        });

        // Editor body
        var editor = document.createElement('div');
        editor.className = 'wysiwyg-editor';
        editor.contentEditable = true;
        editor.innerHTML = textarea.value || '';
        editor.style.cssText = 'min-height:160px;padding:16px;outline:none;font-family:Manrope,sans-serif;font-size:14px;color:#0F172A;line-height:1.7;background:#F8FAFC;cursor:text;overflow-y:auto';
        editor.addEventListener('input', syncContent);
        editor.addEventListener('paste', function (e) {
            e.preventDefault();
            var text = (e.clipboardData || window.clipboardData).getData('text/plain');
            document.execCommand('insertText', false, text);
            syncContent();
        });

        wrapper.addEventListener('focusin', function () { wrapper.style.borderColor = '#EAB308'; });
        wrapper.addEventListener('focusout', function () { wrapper.style.borderColor = '#E2E8F0'; });

        function syncContent() {
            textarea.value = editor.innerHTML;
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
            updateToolbarState();
        }

        function updateToolbarState() {
            var btns = toolbar.querySelectorAll('[data-cmd]');
            btns.forEach(function (b) {
                if (b.dataset.cmd === 'source' || b.dataset.cmd === 'link') return;
                var active = document.queryCommandState(b.dataset.cmd);
                if (active) {
                    b.style.background = '#EAB30820';
                    b.style.color = '#EAB308';
                    b.classList.add('active');
                } else {
                    b.style.background = 'transparent';
                    b.style.color = '#64748B';
                    b.classList.remove('active');
                }
            });
        }

        // Source toggle
        var sourceMode = false;
        var sourceTextarea = null;

        function toggleSource(ed, ta, btn) {
            sourceMode = !sourceMode;
            if (sourceMode) {
                if (!sourceTextarea) {
                    sourceTextarea = document.createElement('textarea');
                    sourceTextarea.style.cssText = 'width:100%;min-height:160px;padding:16px;border:none;outline:none;font-family:Manrope,sans-serif;font-size:13px;color:#0F172A;line-height:1.6;background:#F8FAFC;resize:vertical';
                    sourceTextarea.addEventListener('input', function () {
                        textarea.value = sourceTextarea.value;
                    });
                }
                sourceTextarea.value = ed.innerHTML;
                ed.style.display = 'none';
                ed.parentNode.insertBefore(sourceTextarea, ed.nextSibling);
                btn.style.background = '#EAB30820';
                btn.style.color = '#EAB308';
            } else {
                if (sourceTextarea && sourceTextarea.parentNode) {
                    ed.innerHTML = sourceTextarea.value;
                    sourceTextarea.parentNode.removeChild(sourceTextarea);
                }
                ed.style.display = '';
                btn.style.background = 'transparent';
                btn.style.color = '#64748B';
                syncContent();
            }
        }

        // Hide textarea, insert wrapper
        textarea.style.display = 'none';
        textarea.parentNode.insertBefore(wrapper, textarea.nextSibling);
        wrapper.appendChild(toolbar);
        wrapper.appendChild(editor);

        // Ensure content syncs before form submit
        var form = textarea.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                if (sourceTextarea && sourceTextarea.parentNode) {
                    textarea.value = sourceTextarea.value;
                } else {
                    textarea.value = editor.innerHTML;
                }
            });
        }
    }

    // Auto-init all .wysiwyg textareas
    function initAll() {
        document.querySelectorAll('textarea.wysiwyg').forEach(initWysiwyg);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    function getEditor(textareaId) {
        var ta = document.getElementById(textareaId);
        if (!ta || ta.tagName !== 'TEXTAREA') return null;
        var wrapper = ta.nextElementSibling;
        if (!wrapper || !wrapper.classList.contains('wysiwyg-wrapper')) return null;
        return { textarea: ta, wrapper: wrapper, editor: wrapper.querySelector('.wysiwyg-editor') };
    }

    function setContent(textareaId, html) {
        var ctx = getEditor(textareaId);
        if (!ctx || !ctx.editor) return;
        ctx.editor.innerHTML = html;
        ctx.textarea.value = html;
    }

    function getContent(textareaId) {
        var ctx = getEditor(textareaId);
        return ctx ? ctx.textarea.value : '';
    }

    // Expose for dynamic usage
    window.WYSIWYG = { init: initWysiwyg, initAll: initAll, setContent: setContent, getContent: getContent, getEditor: getEditor };
})();
