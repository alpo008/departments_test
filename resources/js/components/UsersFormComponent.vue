<template>
    <div class="card">
        <div class="card-header">{{ headingText }}</div>
        <div class="card-body">
            <form @submit.prevent.stop="save">
                <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input type="text"
                               class="form-control"
                               :class="getError('name') ? 'is-invalid' : ''"
                               id="inputName"
                               placeholder="Enter name"
                               v-model="user.name"
                        >
                        <span role="alert" class="invalid-feedback" v-if="getError('name')">
                            <strong>{{ getError('name') }}</strong>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email"
                               class="form-control"
                               :class="getError('name') ? 'is-invalid' : ''"
                               id="inputEmail"
                               placeholder="Enter e-mail"
                               v-model="user.email"
                        >
                        <span role="alert" class="invalid-feedback" v-if="getError('name')">
                            <strong>{{ getError('email') }}</strong>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password"
                               class="form-control"
                               :class="getError('password') ? 'is-invalid' : ''"
                               id="inputPassword"
                               placeholder="Enter password"
                               v-model="user.password"
                        >
                        <span role="alert" class="invalid-feedback" v-if="getError('name')">
                            <strong>{{ getError('password') }}</strong>
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "UsersFormComponent",
    data() {
        return {
            indexUrl: '/user',
            user: {},
            id: 0,
            errors: {}
        };
    },
    methods: {
        getUser() {
            axios.get(this.indexUrl + '/' + this.id)
                .then(result => {
                    if (result.status === 200 && typeof result.data !== 'undefined') {
                        this.user = typeof result.data.user === 'object' &&
                        !jQuery.isEmptyObject(result.data.user) ?
                            result.data.user : {}
                    }
                })
                .catch(errors => this.handleErrors(errors));
        },
        save() {

        },
        handleErrors(errors) {
            alert(errors.toString())
        },
        getError(name) {
            let errors = this.errors[name]
            if (typeof errors === 'string') {
                return errors
            }
            if (typeof errors === 'object' && typeof errors[0] === 'string') {
                return errors[0]
            }
            return false;
        }
    },
    computed: {
        headingText() {
            if (!this.id) {
                return 'Add user'
            }
            return 'Update user'
        }
    },
    beforeMount() {
        let id = parseInt(this.$route.params.id);
        if (!isNaN(id)) {
            this.id = id;
        }
        this.getUser()
    }
}
</script>

<style scoped>
input[type='password'] {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
