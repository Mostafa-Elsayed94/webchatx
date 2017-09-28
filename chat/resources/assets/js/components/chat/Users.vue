<template>
    <div class="users">
        <div class="users__header" v-if="users.length">{{ users.length - 1 }} {{ pluralize('user', (users.length - 1) )}} online</div>
        <div class="users__header" v-else>loading users...</div>
        <div class="users__user" v-for="user in users" v-if="user.id != loggedUser.id">
            <a href="#" @click="talk(user.id)">{{ user.name }}</a>
        </div>
    </div>
</template>

<script>
    import pluralize from 'pluralize'
    import Bus from '../../bus'

    export default {
        data() {
            return {
                users: [],
                loggedUser : Laravel.user
            };
        },
        methods: {
            pluralize: pluralize,
            talk(userId) {
                // console.log(userId);
                Bus.$emit('talk', userId);
            }
        },
        mounted() {
            Bus.$on('users.here', (users) => {
                this.users = users;
            }).$on('users.joined', (user) => {
                this.users.unshift(user);
            }).$on('users.left', (user) => {
                this.users = this.users.filter((u) => {
                    return u.id !== user.id;
                });
            });
        }
    }    
</script>

<style lang="scss">
    .users {
        background-color: #FFF;
        border: 1px solid #D3E0E9;
        border-radius: 3px;

        &__header {
            padding: 15px;
            font-weight: 800;
            margin: 0;
        }

        &__user {
            padding: 0 15px;

            &:last-child {
                padding-bottom: 15px;
            }
        }
    }
</style>