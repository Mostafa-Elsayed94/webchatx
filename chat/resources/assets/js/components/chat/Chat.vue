<template>
    <div class="chat" :class="{'hide_chat' : !startedConversation}">
        <chat-messages></chat-messages>
        <form action="" class="chat__form">
            <textarea 
                id="body"
                cols="30"
                rows="4"
                class="chat__form-input"
                v-model="body"
                @keydown="handleMessageInput"
            ></textarea>
            <span class="chat__form-helptext">
                Hit return to send or Shift + Return for a new line
            </span>
        </form>
    </div>
</template>

<script>
    import Bus from '../../bus'
    import moment from 'moment'

    export default {
        data() {
            return {
                body: null,
                bodyBackedUp: null,
                startedConversation: false,
                conversation_id: null,
            }
        },
        methods: {
            handleMessageInput(e) {
                this.bodyBackedUp = this.body;

                if (e.keyCode === 13 && !e.shiftKey) {
                    e.preventDefault();
                    this.send();
                }
            },
            buildTempMessage() {
                let tempId = Date.now();

                return {
                    id: tempId,
                    body: this.body.trim(),
                    created_at: moment().utc(0).format('YYYY-MM-DD HH:mm:ss'),
                    selfOwned: true,
                    conversation_id: this.conversation_id,
                    to: this.to,
                    user: {
                        name: Laravel.user.name,
                        id: Laravel.user.id
                    }
                }
            },
            send() {
                if (!this.body || this.body.trim()  === '') {
                    return;
                }

                let tempMessage = this.buildTempMessage();

                Bus.$emit('message.added', tempMessage);

                axios.post('chat/messages', {
                    body: this.body.trim(),
                    conversation_id: this.conversation_id,
                    to: this.to
                })
                .then((response) => {
                    if (!this.conversation_id) {
                        this.conversation_id = response.data.conversation_id;
                    }
                })
                .catch(() => {
                    this.body = this.bodyBackedUp;
                    Bus.$emit('message.removed', tempMessage);
                });

                this.body = null;
            }
        },
        mounted() {
            Bus.$on('talk', (userId) => {
                this.startedConversation = true;
                this.to = userId;
                axios.get('conversation/' + Laravel.user.id + '/' + userId).then((response) => {
                    this.conversation_id = response.data.conversation_id;
                    this.to = userId;
                    Bus.$emit('messages.retrieved', response.data);
                });
            });
        }
    }
</script>

<style lang="scss">
    .hide_chat {
        display: none;
    }

    .chat {
        background-color: #FFF;
        border: 1px solid #D3E0E9;
        border-radius: 3px;

        &__form {
            border-top: 1px solid #D3E0E9;
            padding: 10px;
            
            &-input {
                width: 100%;
                border-top: 1px solid #D3E0E9;
                padding: 5px 10px;
                outline: none;
            }

            &-helptext {
                color: #AAA;
            }
        }
    }
    

</style>