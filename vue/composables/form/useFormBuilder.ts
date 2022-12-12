/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */
import { ref } from 'vue'
import IFormField from '@/composables/form/IFormField'
import dateHelper from '@/helpers/dateHelper'

const useFormBuilder = () => {
    const formFields = ref<IFormField[]>([])
    const createFormFields = (fields: IFormField[]) => {
        formFields.value = fields
    }

    const setFormValues = (data: any) => {
        formFields.value = formFields.value.map(field => data[field.key] === undefined ? field : {
            ...field,
            value: data[field.key] ? transformData(field.type, data[field.key]) : null
        })
    }

    const transformData = (type: IFormField['type'], value: any) => {
        if (type === 'datetime') {
            return dateHelper().toCzDateTime(value)
        }

        return value;
    }

    return {
        createFormFields,
        setFormValues,
        formFields
    }
}

export default useFormBuilder