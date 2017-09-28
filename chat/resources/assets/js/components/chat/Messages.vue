<template>
    <div class="chat__messages" ref="messages">
        <chat-message v-for="message in messages" :key="message.id" :message="message"></chat-message>
    </div>
</template>

<script>
    import Bus from '../../bus'
    export default {
        data() {
            return {
                conversation_id: null,
                messages: []
            }
        },
        methods: {
            removeMessage(id) {
                this.messages = this.messages.filter((message) => {
                    return message.id !== id;
                });
            }
        },
        mounted() {
            Bus.$on('message.added', (message) => {
                if (this.conversation_id == message.conversation_id) {
                    this.messages.unshift(message);
                }
                
                if (message.selfOwned) {
                    this.$refs.messages.scrollTop = 0;
                }
            }).$on('message.removed', (message) => {
                this.removeMessage(message.id);
            }).$on('messages.retrieved', (data) => {
                this.messages = data.messages;
                this.conversation_id = data.conversation_id;
                // console.log('we are here');
                // console.log(data);
            });
        }
    }
</script>

<style lang="scss">
    .chat {
        &__messages {
            height: 400px;
            max-height: 400px;
            overflow-y: scroll;
        }
    }
</style>