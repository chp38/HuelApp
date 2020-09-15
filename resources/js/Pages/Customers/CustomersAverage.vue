<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Average Across All Customers
            </h2>
        </template>
        <p>
            Average for all orders(total: {{ totalOrders }}): {{ customersAverage }}
        </p>
        <p>
            Average For Customer {{ customerName }}: {{ customerAverage }}
        </p>
        <p>
            Average For Variant {{ variantName }}: {{ variantAverage }}
        </p>
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
                customersAverage: null,
                customerAverage: null,
                variantAverage: null,
                totalOrders: null,
                customerName: null,
                variantName: null
            }
        },
        mounted () {
            axios.get('/api/variant/39/average-order-value', {
                headers: {
                    'X-XSRF-TOKEN': Csrf.getCookie(),
                },
            }).then(response => {
                this.variantAverage = response.data.average;
                this.variantName = response.data.variantName;
            });
            axios.get('/api/customer/10/average-order-value', {
                headers: {
                    'X-XSRF-TOKEN': Csrf.getCookie(),
                },
            }).then(response => {
                this.customerAverage = response.data.average;
                this.customerName = response.data.customerName;
            });
            axios.get('/api/customers/average-order-value', {
                headers: {
                    'X-XSRF-TOKEN': Csrf.getCookie(),
                },
            }).then(response => {
                this.customersAverage = response.data.average;
                this.totalOrders = response.data.totalOrders;
            });
        }
    }
</script>
