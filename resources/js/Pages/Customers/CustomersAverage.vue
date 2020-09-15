<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Average Across All Customers
            </h2>
        </template>

        {{ average }}
    </app-layout>
</template>

<script>
    import AppLayout from './../../Layouts/AppLayout'
    import Welcome from './../../Jetstream/Welcome'
    import Csrf from "../Profile/Csrf";
    import axios from "axios";

    export default {
        components: {
            AppLayout,
            Welcome,
        },
        data () {
            return {
                average: null
            }
        },
        mounted () {
            axios.get('/api/variant/10/average-order-value', {
                headers: {
                    'X-XSRF-TOKEN': Csrf.getCookie(),
                },
            }).then(response => {
                this.average = response.data
            });
        }
    }
</script>
