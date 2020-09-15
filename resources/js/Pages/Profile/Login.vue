<template>
    <guest-layout>
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <login-form-section @submitted="login">
                <template #form>
                    <div class="mt-4">
                        <jet-label value="Email" />
                        <jet-input v-model="form.email" class="block mt-1 w-full" type="email" name="email" required autofocus />
                    </div>

                    <div class="mt-4">
                        <jet-label value="Password" />
                        <jet-input v-model="form.password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                    </div>
                </template>

                <template #actions>
                    <a class="underline text-sm text-gray-600 hover:text-gray-900"
                       href="/register">
                        Register
                    </a><jet-button :class="{ 'opacity-25': login.processing }" :disabled="login.processing">
                        Login
                    </jet-button>
                </template>
            </login-form-section>
        </div>
    </guest-layout>

</template>

<script>
    import JetLabel from "../../Jetstream/Label"
    import JetActionMessage from "../../Jetstream/ActionMessage"
    import LoginFormSection from "../../Jetstream/LoginFormSection"
    import JetButton from "../../Jetstream/Button"
    import JetInput from "../../Jetstream/Input"
    import User from "./User";
    import GuestLayout from './../../Layouts/GuestLayout'
    import JetApplicationLogo from './../../Jetstream/ApplicationLogo'
    import JetApplicationMark from './../../Jetstream/ApplicationMark'
    import JetDropdown from './../../Jetstream/Dropdown'
    import JetDropdownLink from './../../Jetstream/DropdownLink'
    import JetNavLink from './../../Jetstream/NavLink'
    import JetResponsiveNavLink from './../../Jetstream/ResponsiveNavLink'

    export default {
        components: {
            JetApplicationLogo,
            JetApplicationMark,
            JetDropdown,
            JetDropdownLink,
            JetNavLink,
            JetResponsiveNavLink,
            GuestLayout,
            JetLabel,
            JetInput,
            JetButton,
            LoginFormSection,
            JetActionMessage
        },
        data() {
            return {
                form: {
                    email: "",
                    password: ""
                },
                errors: []
            };
        },
        methods: {
            login() {
                User.login(this.form)
                    .then(() => {
                        this.$root.$emit("login", true);
                        localStorage.setItem("auth", "true");
                        this.$inertia.visit('/dashboard');
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors;
                        }
                    });
            }
        },
    };
</script>