<template>
    <div class="users">
        <div class="loading" v-if="loading">
            Loading...
        </div>

        <div class="error" v-if="error">
            {{ error }}

            <p>
                <button @click.prevent="fetchData">
                    Try Again
                </button>
            </p>
        </div>

        <ul v-if="users">
            <li v-for="{ id, name, email } in users" :key="id">
                <strong>Name:</strong>{{ name }}
                <strong>Email:</strong>{{ email }} |
                <router-link :to="{ name: 'users.edit', params:{ id } }">Edit</router-link>
            </li>
            <div>
                <router-link :to="{ name: 'users.create' }">Add User </router-link>
            </div>
        </ul>

        <div class="pagination">
            <button :disabled="!prevPage" @click.prevent="goToPrev">Previous</button>
            {{ paginationCount }}
            <button :disabled="!nextPage" @click.prevent="goToNext">Next</button>
        </div>
    </div>
</template>
<script>
import api from '../api/users'

const getUsers = (page, callback) => {
    const params = { page };
    api.all({params})
    .then(response => {
        callback(null, response.data);
    }).catch(error => {
        callback(error, error.response.data);
    });
}

export default {
    data() {
        return {
            loading: false,
            users: null,
            meta: null,
            links: {
                first: null,
                last: null,
                next: null,
                prev: null,
            },
            error: null,
        };
    },
    computed: {
        nextPage() {
            if (! this.meta || this.meta.current_page === this.meta.last_page) {
                return;
            }

            return this.meta.current_page + 1;
        },
        prevPage() {
            if (! this.meta || this.meta.current_page === 1) {
                return;
            }

            return this.meta.current_page - 1;
        },
        paginationCount() {
            if (!this.meta) {
                return
            }
            const { current_page, last_page } = this.meta;
            return `${current_page} / ${last_page}`;
        }
    },
    beforeRouteEnter (to, from, next) {
        getUsers(to.query.page, (err, data) => {
            next(vm => vm.setData(err, data));
        });
    },
    // 当路由更改并且组件已经渲染时，
    // 逻辑会略有不同。
    beforeRouteUpdate (to, from, next) {
        this.users = this.links = this.meta = null
        getUsers(to.query.page, (err, data) => {
            this.setData(err, data);
            next();
        });
    },
    // created() {
    //     this.fetchData()
    // },
    methods: {
        // fetchData() {
        //     this.error = this.users = null;
        //     this.loading = true;
        //     axios.get('/api/users').then(response => {
        //         console.log(response);
        //         this.loading = false;
        //         this.users = response.data.data;
        //     }).catch(error => {
        //         this.loading = false;
        //         this.error = error.response.data.message || error.message;
        //     });
        // },

        // data:users 将data变量赋值给users
        setData(err, { data: users, links, meta }) {
            if (err) {
                this.err = err.toString();
            } else {
                this.users = users;
                this.links = links;
                this.meta = meta;
            }
        },
        goToNext() {
            this.$router.push({
                query: {
                    page: this.nextPage,
                },
            });
        },
        goToPrev() {
            this.$router.push({
                // 加上这个后，点击前一页会直接跳转到首页
                // name: 'users.index', 
                query: {
                    page: this.prevPage,
                }
            });
        },
    },
}
</script>