class coffinComment extends coffinBase {
    loading = false;
    constructor() {
        super();
        this.init();
    }

    private init() {
        if (document.querySelector('.comment-form')) {
            document.querySelector('.comment-form')?.addEventListener('submit', (e) => {
                e.preventDefault();
                if (this.loading) return;
                const form = document.querySelector('.comment-form') as HTMLFormElement;
                const formData = new FormData(form);
                const formDataObj: { [index: string]: any } = {};
                formData.forEach((value, key: any) => (formDataObj[key] = value));
                this.loading = true;
                fetch(this.obvInit.restfulBase + 'coffin/v1/comment', {
                    method: 'POST',
                    body: JSON.stringify(formDataObj),
                    headers: {
                        'X-WP-Nonce': this.obvInit.nonce,
                        'Content-Type': 'application/json',
                    },
                })
                    .then((response) => {
                        return response.json();
                    })
                    .then((data) => {
                        this.loading = false;
                        if (data.code != 200) {
                            return this.showNotice(data.message, 'error');
                        }
                        let a = document.getElementById('cancel-comment-reply-link'),
                            i = document.getElementById('respond'),
                            n = document.getElementById('wp-temp-form-div');
                        const comment = data.data;
                        const html = `<li class="comment cComment--item" id="comment-${comment.comment_ID}">
                        <article class="cComment--block comment-body__fresh">
                            <footer class="cComment--info">
                                <div class="cComment--avatar">
                                    <img alt="" src="${comment.author_avatar_urls}" class="avatar" height="42" width="42" />
                                </div>
                                <div class="cComment--meta">
                                    <div class="cComment--author">${comment.comment_author}</div>
                                    <time class="cComment--time">${this.obvInit.now_text}</time>
                                </div>
                                </div>
                            </footer>
                            <div class="cComment--content">
                                ${comment.comment_content}
                            </div>
                        </article>
                    </li>`;
                        const parent_id = (
                            document.querySelector('#comment_parent') as HTMLInputElement
                        )?.value;
                        if (a) {
                            a.style.display = 'none';
                            a.onclick = null;
                        }
                        const commentParentInput = document.getElementById(
                            'comment_parent'
                        ) as HTMLInputElement | null;
                        if (commentParentInput) {
                            commentParentInput.value = '0';
                        }
                        n &&
                            i &&
                            n.parentNode &&
                            (n.parentNode.insertBefore(i, n), n.parentNode.removeChild(n));
                        if (document.querySelector('.comment-body__fresh'))
                            document
                                .querySelector('.comment-body__fresh')
                                ?.classList.remove('comment-body__fresh');
                        const commentInput = document.getElementById(
                            'comment'
                        ) as HTMLInputElement | null;
                        if (commentInput) {
                            commentInput.value = '';
                        }
                        if (parent_id != '0') {
                            document
                                .querySelector('#comment-' + parent_id)
                                ?.insertAdjacentHTML(
                                    'beforeend',
                                    '<ol class="children">' + html + '</ol>'
                                );
                            console.log(parent_id);
                        } else {
                            if (document.querySelector('.no--comment')) {
                                document.querySelector('.no--comment')?.remove();
                            }
                            document
                                .querySelector('.cComment--list')
                                ?.insertAdjacentHTML('beforeend', html);
                        }

                        const newComment = document.querySelector(
                            `#comment-${comment.comment_ID}`
                        ) as HTMLElement;

                        if (newComment) {
                            newComment.scrollIntoView({ behavior: 'smooth' });
                        }

                        this.showNotice(this.obvInit.comment_success_text);
                    });
            });
        }
    }
}

new coffinComment();
